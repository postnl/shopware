<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\Shopware;

use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Rule\AlwaysValidRule;
use Shopware\Core\Checkout\Cart\Rule\CartAmountRule;
use Shopware\Core\Checkout\Shipping\ShippingMethodCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\DeliveryTime\DeliveryTimeDefinition;
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
        $shippingMethod = $this->getExistingShippingMethods($context);

        try {
            $rule = $this->getCartAmountRule($context);
            $deliveryTime = $this->getDeliveryTime($context);
        } catch(\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw $e;
        }

        // Create the shipping method
        $event = $this->shippingMethodRepository->upsert([
            [
                'id' => Uuid::randomHex(),
                'name' => 'PostNL',
                'active' => false,
                'availabilityRuleId' => $rule->getId(),
                'deliveryTimeId' => $deliveryTime->getId(),
                'mediaId' => $this->getMediaId($pluginDir, $context),
                'customFields' => [
                    'postnl_shipments' => [
                        'deliveryType' => 'shipment'
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

    /**
     * @param Context $context
     * @return ShippingMethodCollection
     */
    private function getExistingShippingMethods(Context $context): ShippingMethodCollection
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('customFields.postnl_shipments.deliveryType', [
            'shipment',
            'pickup',
            'mailbox'
        ]));

        /** @var ShippingMethodCollection $shippingMethods */
        $shippingMethods = $this->shippingMethodRepository->search($criteria, $context)->getEntities();

        return $shippingMethods;
    }

    /**
     * Returns the always valid rule.
     * @param Context $context
     * @return RuleEntity|null
     * @throws \Exception
     * @deprecated
     */
    private function getAlwaysValidRule(Context $context): RuleEntity
    {
        return $this->getCartAmountRule($context);

//        $criteria = new Criteria();
//        $criteria->addFilter(new EqualsFilter('conditions.type', (new AlwaysValidRule())->getName()));
//
//        $rule = $this->ruleRepository->search($criteria, $context)->first();
//
//        if($rule instanceof RuleEntity) {
//            return $rule;
//        }

        // Do write here but Shopware throws error for isAlwaysValid
//        [
//            'name' => 'Always valid (Default)',
//            'priority' => 100,
//            'conditions' => [
//                [
//                    'type' => (new AlwaysValidRule())->getName(),
//                    'value' => ['isAlwaysValid' => true]
//                ]
//            ]
//        ]
    }

    /**
     * Returns an availability rule. Creates it if it does not exist.
     *
     * @param Context $context
     * @return RuleEntity
     * @throws \Exception
     */
    private function getCartAmountRule(Context $context): RuleEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('moduleTypes', null));
        $criteria->addFilter(new EqualsFilter('conditions.type', (new CartAmountRule())->getName()));
        $criteria->addFilter(new EqualsFilter('conditions.value.amount', 0));
        $criteria->addFilter(new EqualsFilter('conditions.value.operator', CartAmountRule::OPERATOR_GTE));

        $rule = $this->ruleRepository->search($criteria, $context)->first();

        if ($rule instanceof RuleEntity) {
            return $rule;
        }

        $writeEvents = $this->ruleRepository->create(
            [
                [
                    'name' => 'Cart >= 0',
                    'priority' => 500,
                    'conditions' => [
                        [
                            'type' => (new CartAmountRule())->getName(),
                            'value' => [
                                'amount' => 0,
                                'operator' => CartAmountRule::OPERATOR_GTE
                            ]
                        ]
                    ]
                ]
            ],
            $context
        )->getEventByEntityName(RuleDefinition::ENTITY_NAME);

        if(count($writeEvents->getWriteResults()) > 0){
            return $this->getCartAmountRule($context);
        }

        throw new \Exception('Could not get availability rule for shipping methods');
    }

    /**
     * Returns a delivery time. Creates it if it does not exist.
     *
     * @param Context $context
     * @return DeliveryTimeEntity|null
     * @throws \Exception
     */
    private function getDeliveryTime(Context $context): DeliveryTimeEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('min', 1));
        $criteria->addFilter(new EqualsFilter('max', 3));
        $criteria->addFilter(new EqualsFilter('unit', 'day'));

        $deliveryTime = $this->deliveryTimeRepository->search($criteria, $context)->first();

        if ($deliveryTime instanceof DeliveryTimeEntity) {
            return $deliveryTime;
        }

        $writeEvents = $this->deliveryTimeRepository->create(
            [
                [
                    'name' => '1-3 days',
                    'min' => 1,
                    'max' => 3,
                    'unit' => 'day',
                ]
            ],
            $context
        )->getEventByEntityName(DeliveryTimeDefinition::ENTITY_NAME);

        if(count($writeEvents->getWriteResults()) > 0){
            return $this->getDeliveryTime($context);
        }

        throw new \Exception('Could not get delivery time for shipping methods');
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
