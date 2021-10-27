<?php declare(strict_types=1);

namespace PostNl\Shipments\Service\ShippingMethod;

use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Rule\AlwaysValidRule;
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
    private $ruleRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(
        EntityRepositoryInterface $deliveryTimeRepository,
        EntityRepositoryInterface $ruleRepository,
        EntityRepositoryInterface $shippingMethodRepository,
        LoggerInterface $logger
    )
    {
        $this->deliveryTimeRepository = $deliveryTimeRepository;
        $this->ruleRepository = $ruleRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->logger = $logger;
    }

    public function createShippingMethod(Context $context): void
    {
        $id = Uuid::randomHex();

        // TODO get existing shipping method

        $rule = $this->getAlwaysValidRule($context);
        $deliveryTime = $this->getDeliveryTime($context);

        // Create the shipping method
        $event = $this->shippingMethodRepository->upsert([
            [
                'id' => $id,
                'name' => 'PostNL',
                'active' => false,
                'availabilityRule' => $rule
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
                'deliveryTime' => $deliveryTime
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
        return $this->deliveryTimeRepository->search(new Criteria(), $context)->first();
    }
}
