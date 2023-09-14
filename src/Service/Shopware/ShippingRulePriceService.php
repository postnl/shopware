<?php

namespace PostNL\Shopware6\Service\Shopware;

use PostNL\Shopware6\Defaults as PostNLDefaults;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Shipping\Aggregate\ShippingMethodPrice\ShippingMethodPriceEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class ShippingRulePriceService
{
    /**
     * @var EntityRepository
     */
    private $shippingMethodPricesRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityRepository $shippingMethodPricesRepository
     * @param LoggerInterface  $logger
     */
    public function __construct($shippingMethodPricesRepository, LoggerInterface $logger)
    {
        $this->shippingMethodPricesRepository = $shippingMethodPricesRepository;
        $this->logger = $logger;
    }

    /**
     * Create pricing matrices for a rule
     *
     * @param array   $shippingMethodArray
     * @param array   $ruleIds
     * @param Context $context
     * @return void
     */
    public function createPricingMatrices(array $shippingMethodArray, array $ruleIds, Context $context)
    {
        foreach ($shippingMethodArray as $shippingMethod => $shippingMethodId) {
            switch ($shippingMethod) {
                case 'shipment':
                    $this->createPricingMatrix($shippingMethodId, $ruleIds[PostNLDefaults::ZONE_ONLY_NETHERLANDS], $context);
                    $this->createPricingMatrix($shippingMethodId, $ruleIds[PostNLDefaults::ZONE_ONLY_BELGIUM], $context);
                    $this->createPricingMatrix($shippingMethodId, $ruleIds[PostNLDefaults::ZONE_ONLY_EUROPE], $context);
                    $this->createPricingMatrix($shippingMethodId, $ruleIds[PostNLDefaults::ZONE_ONLY_REST_OF_WORLD], $context);
                    break;
                case 'pickup':
                    $this->createPricingMatrix($shippingMethodId, $ruleIds[PostNLDefaults::ZONE_ONLY_NETHERLANDS], $context);
                    $this->createPricingMatrix($shippingMethodId, $ruleIds[PostNLDefaults::ZONE_ONLY_BELGIUM], $context);
                    break;
            }
        }
    }

    /**
     * @param string  $shippingMethodId
     * @param string  $ruleId
     * @param Context $context
     * @return string|null
     */
    public function createPricingMatrix(string $shippingMethodId, string $ruleId, Context $context): ?string
    {
        //Check if it does not already exist
        $criteria = new Criteria();
        $criteria->addFilter(
            new MultiFilter(
                MultiFilter::CONNECTION_AND,
                [
                    new EqualsFilter('shippingMethodId', $shippingMethodId),
                    new EqualsFilter('ruleId', $ruleId),
                ]
            )
        );

        $result = $this->shippingMethodPricesRepository->search($criteria, $context);

        if ($result->getTotal() > 0) {
            /** @var ShippingMethodPriceEntity $entity */
            $entity = $result->first();
            return $entity->getId();
        }

        //It does not, create.
        $id = Uuid::randomHex();
        $currencyPrice =
            ['c' . Defaults::CURRENCY => [
                'net' => '0',
                'gross' => '0',
                'linked' => false,
                'currencyId' => Defaults::CURRENCY,
            ],
            ];

        $result = $this->shippingMethodPricesRepository->create(
            [
                [
                    'id' => $id,
                    'calculation' => 2,
                    'quantityStart' => 1,
                    'currencyPrice' => $currencyPrice,
                    'shippingMethodId' => $shippingMethodId,
                    'ruleId' => $ruleId,
                    'price' => 0,
                ],
            ], $context);

        if (empty($result->getErrors())) {
            return $id;
        } else {
            $this->logger->error("Could not create Shipping method price",
                [
                    'shippingMethodId' => $shippingMethodId,
                    'ruleId' => $ruleId,
                    'result' => $result,
                ]);
            return null;
        }

    }
}
