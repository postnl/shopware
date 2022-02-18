<?php

namespace PostNL\Shipments\Facade;

use PostNL\Shipments\Entity\Product\ProductDefinition;
use PostNL\Shipments\Entity\Product\ProductEntity;
use PostNL\Shipments\Service\PostNL\Product\ProductService;
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
        string  $sourceZone,
        string  $destinationZone,
        Context $context
    ): array
    {
        if (!$this->sourceZoneHasProducts($sourceZone, $context)) {
            return [];
        }

        return $this->productService->getDeliveryTypes($sourceZone, $destinationZone, $context);
    }

    public function getAvailableFlags(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        Context $context
    ): array
    {
        if (!$this->sourceZoneHasProducts($sourceZone, $context)) {
            return [];
        }

        $products = $this->productService->getProductsByShippingConfiguration(
            $sourceZone,
            $destinationZone,
            $deliveryType,
            $context
        );

        return $this->productService->buildFlagStructs2($products);
    }

    /**
     * @param string $productId
     * @param Context $context
     * @return array
     * @throws \Exception
     */
    public function getFlagsForProduct(string $productId, Context $context): array
    {
        $product = $this->productService->getProduct($productId, $context);

        $products = $this->productService->getProductsByShippingConfiguration(
            $product->getSourceZone(),
            $product->getDestinationZone(),
            $product->getDeliveryType(),
            $context
        );

        $filteredProducts = $this->productService->filterProductsByFlags(
            $products,
            [
                ProductDefinition::PROP_HOME_ALONE => $product->getHomeAlone(),
                ProductDefinition::PROP_RETURN_IF_NOT_HOME => $product->getReturnIfNotHome(),
                ProductDefinition::PROP_INSURANCE => $product->getInsurance(),
                ProductDefinition::PROP_SIGNATURE => $product->getSignature(),
                ProductDefinition::PROP_AGE_CHECK => $product->getAgeCheck(),
                ProductDefinition::PROP_NOTIFICATION => $product->getNotification(),
            ]
        );

        return $this->productService->buildFlagStructs2($filteredProducts);
        // This might go wrong
//        return $this->productService->buildFlagStructs(
//            $products,
//            [
//                ProductDefinition::PROP_HOME_ALONE => $product->getHomeAlone(),
//                ProductDefinition::PROP_RETURN_IF_NOT_HOME => $product->getReturnIfNotHome(),
//                ProductDefinition::PROP_INSURANCE => $product->getInsurance(),
//                ProductDefinition::PROP_SIGNATURE => $product->getSignature(),
//                ProductDefinition::PROP_AGE_CHECK => $product->getAgeCheck(),
//                ProductDefinition::PROP_NOTIFICATION => $product->getNotification(),
//            ]
//        );
    }

    /**
     * @param string $productId
     * @param Context $context
     * @return array
     * @throws \Exception
     */
    public function getProduct(string $productId, Context $context): ProductEntity
    {
        return $this->productService->getProduct($productId, $context);
    }

    public function getDefaultProduct(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        Context $context
    ): ProductEntity
    {
        return $this->productService->getDefaultProduct($sourceZone, $destinationZone, $deliveryType, $context);
    }

    public function selectProduct(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array   $flags,
        array   $changeSet,
        Context $context
    ): ProductEntity
    {
        $flags = $this->fixFlagBoolean($flags);
        $changeSet = $this->fixChangeSetBoolean($changeSet);

        $products = $this->productService->getProductsByShippingConfiguration(
            $sourceZone,
            $destinationZone,
            $deliveryType,
            $context
        );

        $filters = $this->productService->getFlagsForProductSelection(
            $sourceZone,
            $destinationZone,
            $deliveryType,
            $flags,
            $changeSet,
            $context
        );
        // Should always only be one.
        $filteredProducts = $this->productService->filterProductsByFlags($products, $filters);

        return $filteredProducts->first();
    }

    protected function fixFlagBoolean(array $flags): array
    {
        $fixed = [];
        foreach ($flags as $key => $value) {
            $fixed[$key] = $value === "true";
        }
        return $fixed;
    }

    protected function fixChangeSetBoolean(array $changeSet): array
    {
        $fixed = [];
        foreach ($changeSet as $change) {
            $change['selected'] = $change['selected'] === 'true';
            $fixed[] = $change;
        }
        return $fixed;
    }
}
