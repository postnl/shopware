<?php

namespace PostNL\Shipments\Facade;

use PostNL\Shipments\Entity\Product\ProductEntity;
use PostNL\Shipments\Service\PostNL\ProductCode\ProductService;
use PostNL\Shipments\Struct\ProductOptionStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;

class ProductFacade
{
    /**
     * @var ProductService
     */
    protected $productCodeService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ProductService  $productCodeService,
        LoggerInterface $logger
    )
    {
        $this->productCodeService = $productCodeService;
        $this->logger = $logger;
    }

    public function sourceZoneHasProducts(string $sourceZone, Context $context): bool
    {
        return $this->productCodeService->sourceZoneHasProducts($sourceZone, $context);
    }

    public function getAvailableDeliveryTypes(
        string $sourceZone,
        string $destinationZone,
        Context $context
    ): array
    {
        if(!$this->sourceZoneHasProducts($sourceZone, $context)) {
            return [];
        }

        return $this->productCodeService->getAvailableDeliveryTypes($sourceZone, $destinationZone, $context);
    }

    public function getAvailableOptions(
        string $sourceZone,
        string $destinationZone,
        string $deliveryType,
        Context $context
    ): array
    {
        if(!$this->sourceZoneHasProducts($sourceZone, $context)) {
            return [];
        }

        return $this->productCodeService->getOptions($sourceZone, $destinationZone, $deliveryType, [], $context);
    }



    public function select(
        string $sourceZone,
        string $destinationZone,
        string $deliveryType,
        array $options,
        Context $context
    ): ProductEntity
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
     * @return ProductOptionStruct[]
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
