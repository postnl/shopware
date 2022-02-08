<?php

namespace PostNL\Shipments\Facade;

use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigEntity;
use PostNL\Shipments\Service\PostNL\ProductCode\ProductCodeService;
use PostNL\Shipments\Struct\ProductCodeOptionStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;

class ProductFacade
{
    /**
     * @var ProductCodeService
     */
    protected $productCodeService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ProductCodeService $productCodeService,
        LoggerInterface $logger
    )
    {
        $this->productCodeService = $productCodeService;
        $this->logger = $logger;
    }

    public function select(
        string $sourceZone,
        string $destinationZone,
        string $deliveryType,
        array $options,
        Context $context
    ): ProductCodeConfigEntity
    {
        try {
            return $this->productCodeService->getProduct($sourceZone, $destinationZone, $deliveryType, $options, $context);
        } catch (\Throwable $e) {
            return $this->productCodeService->getProducts($sourceZone, $destinationZone, $deliveryType, [], $context)->first();
        }
    }

    /**
     * @param string $sourceZone
     * @param string $destinationZone
     * @param string $deliveryType
     * @param array $options
     * @param Context $context
     * @return ProductCodeOptionStruct[]
     */
    public function options(
        string $sourceZone,
        string $destinationZone,
        string $deliveryType,
        array $options,
        Context $context
    ): array
    {
        return $this->productCodeService->getOptions($sourceZone, $destinationZone, $deliveryType, $options, $context);
    }
}
