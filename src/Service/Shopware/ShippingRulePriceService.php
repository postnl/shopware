<?php

namespace PostNL\Shopware6\Service\Shopware;

use Psr\Log\LoggerInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

class ShippingRulePriceService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $shippingMethodPricesRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityRepositoryInterface $shippingMethodPricesRepository
     * @param LoggerInterface $logger
     */
    public function __construct(EntityRepositoryInterface $shippingMethodPricesRepository, LoggerInterface $logger)
    {
        $this->shippingMethodPricesRepository = $shippingMethodPricesRepository;
        $this->logger = $logger;
    }

    /**
     * Create pricing matrices for a rule
     * @param array $shippingMethodArray
     * @param array $ruleIds
     * @param Context $context
     * @return void
     */
    public function createPricingMatrices(array $shippingMethodArray, array $ruleIds,Context $context)
    {
        foreach ($shippingMethodArray as $shippingMethod => $shippingMethodId) {
            switch ($shippingMethod){
                case 'shipment':
                    break;
                case 'pickup':
                    break;
            }
        }
    }

    public function createPricingMatrix(string $shippingMethodId,array $ruleIds,Context $context)
    {
        foreach ( as $item) {

        }
        $this->shippingMethodPricesRepository->create(
            [
                [
                    'calculation'=>2,
                    'quantityStart'=>1,
                    'currencyId'=>Defaults::CURRENCY,
                    'shippingMethodId' => $shippingMethodId,
                    'ruleId' => ''
                ]
            ],$context);
    }
}
