<?php declare(strict_types=1);

namespace PostNL\Shopware6\Facade;

use PostNL\Shopware6\Entity\Product\ProductDefinition;
use PostNL\Shopware6\Entity\Product\ProductEntity;
use PostNL\Shopware6\Service\PostNL\Product\DefaultProductService;
use PostNL\Shopware6\Service\PostNL\Product\ProductService;
use PostNL\Shopware6\Struct\ProductFlagStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;

class ProductFacade
{
    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var DefaultProductService
     */
    protected $defaultProductService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ProductService  $productService,
        DefaultProductService $defaultProductService,
        LoggerInterface $logger
    )
    {
        $this->defaultProductService = $defaultProductService;
        $this->productService = $productService;
        $this->logger = $logger;
    }

    /**
     * @param string  $sourceZone
     * @param Context $context
     * @return bool
     */
    public function sourceZoneHasProducts(string $sourceZone, Context $context): bool
    {
        return $this->productService->sourceZoneHasProducts($sourceZone, $context);
    }

    /**
     * @param string  $sourceZone
     * @param string  $destinationZone
     * @param Context $context
     * @return string[]
     * @throws \Exception
     */
    public function getDeliveryTypes(
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

    /**
     * @param string  $productId
     * @param Context $context
     * @return array<string, ProductFlagStruct>
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

        $productFlags = [];

        foreach(ProductDefinition::ALL_FLAGS as $flag) {
            try {
                $productFlags[$flag] = $product->get($flag);
            } catch(\InvalidArgumentException $e) {
                $this->logger->critical(
                    $e->getMessage(),
                    [
                        'flag' => $flag
                    ]
                );

                throw $e;
            }
        }

        // Only pass in flags that are true
        $filteredFlags = array_filter($productFlags, function ($value) {
            return is_bool($value) && $value;
        });

        $filteredProducts = $this->productService->filterProductsByFlags($products, $filteredFlags);

        return $this->productService->buildFlagStructs($filteredProducts, $filteredFlags);
    }

    /**
     * @param string  $sourceZone
     * @param string  $destinationZone
     * @param string  $deliveryType
     * @param Context $context
     * @return array<string, ProductFlagStruct>
     * @throws \Exception
     */
    public function getFlags(string $sourceZone, string $destinationZone, string $deliveryType, Context $context): array
    {
        $products = $this->productService->getProductsByShippingConfiguration(
            $sourceZone,
            $destinationZone,
            $deliveryType,
            $context
        );

        return $this->productService->buildFlagStructs($products, []);
    }

    /**
     * @param string  $productId
     * @param Context $context
     * @return ProductEntity
     * @throws \Exception
     */
    public function getProduct(string $productId, Context $context): ProductEntity
    {
        return $this->productService->getProduct($productId, $context);
    }

    /**
     * @param string  $sourceZone
     * @param string  $destinationZone
     * @param string  $deliveryType
     * @param Context $context
     * @return ProductEntity
     * @throws \Exception
     */
    public function getDefaultProduct(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        Context $context
    ): ProductEntity
    {
        $productId = $this->defaultProductService->getFallback($sourceZone, $destinationZone, $deliveryType);

        return $this->getProduct($productId, $context);
    }

    /**
     * @param string                 $sourceZone
     * @param string                 $destinationZone
     * @param string                 $deliveryType
     * @param array<string, bool>    $flags
     * @param array<string, mixed>[] $changeSet
     * @param Context                $context
     * @return ProductEntity
     * @throws \Exception
     */
    public function selectProduct(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array   $flags,
        array   $changeSet,
        Context $context
    ): ProductEntity
    {
        $flags = $this->fixBoolean($flags);
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

        // Should have only one, if the combinations are available
        $filteredProducts = $this->productService->filterProductsByFlags($products, $filters);

        $product = $filteredProducts->first();

        if ($product instanceof ProductEntity) {
            return $product;
        }

        // Combination was not available, try to filter by the change set. Can have multiple
        $filteredProducts = $this->productService->filterProductsByChangeSet($products, $changeSet);

        $product = $filteredProducts->first();

        if ($product instanceof ProductEntity) {
            return $product;
        }

        // TODO Exception
        throw new \Exception('Could not select product');
    }

    /**
     * @param array<string, mixed> $flags
     * @param string[]             $keys
     * @return array<string, mixed>
     */
    protected function fixBoolean(array $flags, array $keys = []): array
    {
        $fixed = [];
        foreach ($flags as $key => $value) {
            if (empty($keys) || in_array($key, $keys)) {
                $fixed[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } else {
                $fixed[$key] = $value;
            }
        }
        return $fixed;
    }

    /**
     * @param array<string, mixed>[] $changeSet
     * @return array<string, mixed>[]
     */
    protected function fixChangeSetBoolean(array $changeSet): array
    {
        $fixed = [];
        foreach ($changeSet as $change) {
            $fixed[] = $this->fixBoolean($change, ['selected']);
        }
        return $fixed;
    }
}
