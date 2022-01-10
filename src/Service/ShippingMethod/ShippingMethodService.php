<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\ShippingMethod;

use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Rule\AlwaysValidRule;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\DeliveryTime\DeliveryTimeEntity;

class ShippingMethodService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $deliveryTimeRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $mediaRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(
        EntityRepositoryInterface $deliveryTimeRepository,
        EntityRepositoryInterface $mediaRepository,
        EntityRepositoryInterface $ruleRepository,
        EntityRepositoryInterface $shippingMethodRepository,
        MediaService              $mediaService,
        LoggerInterface           $logger
    )
    {
        $this->deliveryTimeRepository = $deliveryTimeRepository;
        $this->mediaRepository = $mediaRepository;
        $this->ruleRepository = $ruleRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->mediaService = $mediaService;
        $this->logger = $logger;
    }

    public function createShippingMethod(string $pluginDir, Context $context): void
    {
        $shippingMethod = $this->getExistingShippingMethod($context);

        if ($shippingMethod instanceof ShippingMethodEntity) {
            return;
        }

        $rule = $this->getAlwaysValidRule($context);
        $deliveryTime = $this->getDeliveryTime($context);

        // Create the shipping method
        $event = $this->shippingMethodRepository->upsert([
            [
                'id' => Uuid::randomHex(),
                'name' => 'PostNL',
                'active' => false,
                'availabilityRule' => $rule instanceof RuleEntity
                    ? ['id' => $rule->getId()]
                    : [
                        'name' => 'Always valid (Default)',
                        'priority' => 100,
                        'conditions' => [
                            [
                                'type' => (new AlwaysValidRule())->getName(),
                                'value' => json_encode(['isAlwaysValid' => true])
                            ]
                        ]
                    ],
                'deliveryTime' => $deliveryTime instanceof DeliveryTimeEntity
                    ? ['id' => $deliveryTime->getId()]
                    : [
                        'min' => 1,
                        'max' => 3,
                        'unit' => DeliveryTimeEntity::DELIVERY_TIME_DAY,
                        'name' => '1 - 3 days',
                        'translations' => [
                            Defaults::LANGUAGE_SYSTEM => [
                                'name' => '1 - 3 days',
                            ],
                        ],
                    ],
                'mediaId' => $this->getMediaId($pluginDir, $context),
                'prices' => [
                    [
                        'calculation' => 1,
                        'currencyId' => $context->getCurrencyId(),
                        'price' => 0.0,
                        'quantityStart' => 1,
                    ]
                ],
                'customFields' => [
                    'postnl_shipping' => [
                        'id' => 'postnl'
                    ]
                ]
            ],
        ], $context);

        if (!empty($event->getErrors())) {
            $this->logger->error(
                implode(', ', $event->getErrors()),
                $event->getErrors()
            );
        }
    }

    private function getExistingShippingMethod(Context $context): ?ShippingMethodEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('customFields.postnl_shipping.id', 'postnl'));

        return $this->shippingMethodRepository->search($criteria, $context)->first();
    }

    /**
     * Returns the always valid rule.
     *
     * @param Context $context
     *
     * @return RuleEntity|null
     */
    private function getAlwaysValidRule(Context $context): ?RuleEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('conditions.type', (new AlwaysValidRule())->getName()));

        return $this->ruleRepository->search($criteria, $context)->first();
    }

    /**
     * Returns an existing delivery time.
     *
     * @param Context $context
     *
     * @return DeliveryTimeEntity|null
     */
    private function getDeliveryTime(Context $context): ?DeliveryTimeEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('min', 1));
        $criteria->addFilter(new EqualsFilter('max', 3));
        $criteria->addFilter(new EqualsFilter('unit', 'day'));

        return $this->deliveryTimeRepository->search($criteria, $context)->first();
    }

    private function getMediaId(string $pluginDir, Context $context): string
    {
        $fileName = 'postnl-icon';

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('fileName', $fileName));

        $icon = $this->mediaRepository->search($criteria, $context)->first();

        if ($icon instanceof MediaEntity) {
            return $icon->getId();
        }

        // Add icon to the media library
        $iconMime = 'image/svg+xml';
        $iconExt = 'svg';
        $iconPath = realpath(implode(DIRECTORY_SEPARATOR, [
            $pluginDir,
            '..',
            'assets',
            'postnl-logo-vector.svg'
        ]));
        $iconBlob = file_get_contents($iconPath);

        return $this->mediaService->saveFile(
            $iconBlob,
            $iconExt,
            $iconMime,
            $fileName,
            $context,
            '',
            null,
            false
        );
    }
}
