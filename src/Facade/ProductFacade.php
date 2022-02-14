<?php

namespace PostNL\Shipments\Facade;

use PostNL\Shipments\Entity\Product\ProductEntity;
use PostNL\Shipments\Service\PostNL\Product\ProductService;
use PostNL\Shipments\Struct\ProductFlagStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;

class ProductFacade
{
    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ProductService  $productService,
        LoggerInterface $logger
    )
    {
        $this->productService = $productService;
        $this->logger = $logger;
    }

    public function sourceZoneHasProducts(string $sourceZone, Context $context): bool
    {
        return $this->productService->sourceZoneHasProducts($sourceZone, $context);
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

        return $this->productService->getAvailableDeliveryTypes($sourceZone, $destinationZone, $context);
    }

    public function getAvailableFlags(
        string $sourceZone,
        string $destinationZone,
        string $deliveryType,
        Context $context
    ): array
    {
        if(!$this->sourceZoneHasProducts($sourceZone, $context)) {
            return [];
        }

        return $this->productService->getFlags($sourceZone, $destinationZone, $deliveryType, [], $context);
    }

    public function getDefaultProduct(
        string $sourceZone,
        string $destinationZone,
        string $deliveryType,
        Context $context
    ): ProductEntity
    {
        return $this->productService->getDefaultProduct($sourceZone, $destinationZone, $deliveryType, $context);
    }



//    public function select(
//        string $sourceZone,
//        string $destinationZone,
//        string $deliveryType,
//        array $options,
//        Context $context
//    ): ProductEntity
//    {
//        try {
//            return $this->productService->getProduct($sourceZone, $destinationZone, $deliveryType, $options, $context);
//        } catch (\Throwable $e) {
//            return $this->productService->getProducts($sourceZone, $destinationZone, $deliveryType, [], $context)->first();
//        }
//    }
//
//    /**
//     * @param string $sourceZone
//     * @param string $destinationZone
//     * @param string $deliveryType
//     * @param array $options
//     * @param Context $context
//     * @return ProductFlagStruct[]
//     */
//    public function options(
//        string $sourceZone,
//        string $destinationZone,
//        string $deliveryType,
//        array $options,
//        Context $context
//    ): array
//    {
//        return $this->productService->getFlags($sourceZone, $destinationZone, $deliveryType, $options, $context);
//    }
}
