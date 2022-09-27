<?php declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL\Product;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Entity\Product\ProductCollection;
use PostNL\Shopware6\Entity\Product\ProductDefinition;
use PostNL\Shopware6\Entity\Product\ProductEntity;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shopware6\Struct\ProductFlagStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductService
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        EntityRepositoryInterface $productRepository,
        LoggerInterface           $logger
    )
    {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * @param Criteria $criteria
     * @param Context $context
     * @return ProductCollection
     * @throws \Exception
     */
    public function getProducts(Criteria $criteria, Context $context): ProductCollection
    {
        $this->logger->debug('Getting PostNL products', [
            'criteria' => $criteria,
        ]);

        $criteria->addAssociation('options');

        /** @var ProductCollection $products */
        $products = $this->productRepository->search($criteria, $context)->getEntities();

        if ($products->count() > 0) {
            return $products;
        }

        // TODO Unique exception
        throw new \Exception('Could not find valid products');
    }

    /**
     * @param string $productId
     * @param Context $context
     * @return ProductEntity
     * @throws \Exception
     */
    public function getProduct(string $productId, Context $context): ProductEntity
    {
        $this->logger->debug('Getting PostNL product', [
            'productId' => $productId,
        ]);

        $criteria = new Criteria([$productId]);

        try {
            $product = $this->getProducts($criteria, $context)->first();

            if ($product instanceof ProductEntity) {
                return $product;
            }

            // TODO Unique exception
            throw new \Exception('Could not find product');
        } catch (\Exception $e) {
            // TODO Log and Unique exception
            throw $e;
        }
    }

    /**
     * @param string $sourceZone
     * @param string $destinationZone
     * @param string $deliveryType
     * @param Context $context
     * @return ProductCollection
     * @throws \Exception
     */
    public function getProductsByShippingConfiguration(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        Context $context
    ): ProductCollection
    {
        $this->logger->debug("Getting PostNL products by shipping configuration", [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
        ]);

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('sourceZone', $sourceZone),
            new EqualsFilter('destinationZone', $destinationZone),
            new EqualsFilter('deliveryType', $deliveryType)
        );

        return $this->getProducts($criteria, $context);
    }

    /**
     * @param ProductCollection $products
     * @param array<string, mixed> $flags
     * @return ProductCollection
     */
    public function filterProductsByFlags(
        ProductCollection $products,
        array $flags
    ): ProductCollection
    {
        foreach($flags as $k => $v) {
            $products = $products->filterByProperty($k, $v);
        }

        return $products;
    }

    /**
     * @param ProductCollection $products
     * @param array<int, array<string, mixed>> $changeSet
     * @return ProductCollection
     */
    public function filterProductsByChangeSet(
        ProductCollection $products,
        array $changeSet
    ): ProductCollection
    {
        $filteredFlags = $this->getProductFilterFlagsByChangeSet($products, $changeSet);
        return $this->filterProductsByFlags($products, $filteredFlags);
    }

    /**
     * @param ProductCollection $products
     * @param array<int, array<string, mixed>> $changeSet
     * @return array
     */
    public function getProductFilterFlagsByChangeSet(
        ProductCollection $products,
        array $changeSet
    ): array
    {
        $filteredFlags = [];

        foreach($changeSet as $change) {
            $filteredProducts = $products->filterByProperty($change['name'], $change['selected']);

            if($filteredProducts->count() == 0) {
                break;
            }

            $filteredFlags[$change['name']] = $change['selected'];

            $products = $filteredProducts;
        }

        return $filteredFlags;
    }

    /**
     * @param string $sourceZone
     * @param string $destinationZone
     * @param string $deliveryType
     * @param array $currentFlags
     * @param array $changeSet
     * @param Context $context
     * @return array<string, bool>
     * @throws \Exception
     */
    public function getFlagsForProductSelection(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array $currentFlags,
        array $changeSet,
        Context $context
    ): array
    {
        $products = $this->getProductsByShippingConfiguration($sourceZone, $destinationZone, $deliveryType, $context);

        $filteredFlags = $this->getProductFilterFlagsByChangeSet($products, $changeSet);
        $filteredProducts = $this->filterProductsByFlags($products, $filteredFlags);

        $unfilteredFlags = array_diff($this->requiredFlags($destinationZone, $deliveryType), array_keys($filteredFlags));

        foreach($unfilteredFlags as $flag) {
            $availableValues = $filteredProducts->reduceToProperty($flag);

            if(count($availableValues) === 1) {
                $filteredFlags[$flag] = $availableValues[0];
            } else {
                $filteredFlags[$flag] = $currentFlags[$flag];
            }
        }

        return $filteredFlags;
    }

    /**
     * @param ProductCollection $products
     * @param array $selectedFlags
     * @return array<string, ProductFlagStruct>
     */
    public function buildFlagStructs(
        ProductCollection $products,
        array $selectedFlags
    ): array
    {
        $structs = [];

        foreach(ProductDefinition::ALL_FLAGS as $flag) {
            if($flag === ProductDefinition::PROP_AGE_CHECK) {
                continue;
            }

            $availableValues = $products->reduceToProperty($flag);

            /**
             * A flag is visible if there are multiple values, or there is only a single boolean value (i.e. not null)
             * A flag is disabled if there's only 1 possible value, and it's not set to true by the product.
             * A flag is selected if it's set to true by the product, or if there's only 1 possible value that equates to true
             */

            $hasMultipleValues = count($availableValues) > 1;
            $isVisible = $hasMultipleValues || !is_null($availableValues[0]);
            $isDisabled = !array_key_exists($flag, $selectedFlags) && !$hasMultipleValues;
            if(!$hasMultipleValues && !is_null($availableValues[0])) {
                $isSelected = $availableValues[0];
            } else {
                $isSelected = array_key_exists($flag, $selectedFlags);
            }

            $structs[$flag] = new ProductFlagStruct(
                $flag,
                $isVisible,
                $isDisabled,
                $isSelected
            );
        }
        return $structs;
    }

    /**
     * Does the source zone have products? There are only two source zones, NL and BE,
     * but in v1 there are no products for BE yet.
     *
     * @param string $sourceZone
     * @param Context $context
     * @return bool
     */
    public function sourceZoneHasProducts(string $sourceZone, Context $context): bool
    {
        $this->logger->debug('Check source zone products', [
            'sourceZone' => $sourceZone,
        ]);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('sourceZone', $sourceZone));

        try {
            $this->getProducts($criteria, $context);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param string $sourceZone
     * @param string $destinationZone
     * @param Context $context
     * @return array<string>
     * @throws \Exception
     */
    public function getDeliveryTypes(
        string  $sourceZone,
        string  $destinationZone,
        Context $context
    ): array
    {
        $this->logger->debug('Getting delivery types', [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
        ]);

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('sourceZone', $sourceZone),
            new EqualsFilter('destinationZone', $destinationZone)
        );

        try {
            $products = $this->getProducts($criteria, $context);

            return array_values($products->reduceToProperty('deliveryType'));
        } catch (\Throwable $e) {
            // TODO Unique exception
            throw new \Exception('No products found for this zone combination');
        }
    }

    /**
     * @param string $sourceZone
     * @param string $destinationZone
     * @param string $deliveryType
     * @param Context $context
     * @return ProductEntity
     * @throws \Exception
     */
    public function getDefaultProductId(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType
    ): string
    {
        $this->logger->debug('Getting default product', [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
        ]);

        $defaultProductId = null;

        switch($sourceZone) {
            case Zone::NL:
                switch($destinationZone) {
                    case Zone::NL:
                        switch ($deliveryType) {
                            case DeliveryType::MAILBOX:
                                $defaultProductId = Defaults::PRODUCT_MAILBOX_NL_NL;
                                break;
                            case DeliveryType::SHIPMENT:
                                $defaultProductId = Defaults::PRODUCT_SHIPPING_NL_NL;
                                break;
                            case DeliveryType::PICKUP:
                                $defaultProductId = Defaults::PRODUCT_PICKUP_NL_NL;
                                break;
                        }
                        break;
                    case Zone::BE:
                        switch ($deliveryType) {
                            case DeliveryType::SHIPMENT:
                                $defaultProductId = Defaults::PRODUCT_SHIPPING_NL_BE;
                                break;
                            case DeliveryType::PICKUP:
                                $defaultProductId = Defaults::PRODUCT_PICKUP_NL_BE;
                                break;
                        }
                        break;
                    case Zone::EU:
                        $defaultProductId = Defaults::PRODUCT_SHIPPING_NL_EU_4952;
                        break;
                    case Zone::GLOBAL:
                        $defaultProductId = Defaults::PRODUCT_SHIPPING_NL_GLOBAL_4945;
                        break;
                }
                break;
            case Zone::BE:
                switch($destinationZone) {
                    case Zone::BE:
                        switch ($deliveryType) {
                            case DeliveryType::SHIPMENT:
                                $defaultProductId = Defaults::PRODUCT_SHIPPING_BE_BE;
                                break;
                            case DeliveryType::PICKUP:
                                $defaultProductId = Defaults::PRODUCT_PICKUP_BE_BE;
                                break;
                        }
                        break;
                    case Zone::EU:
                        $defaultProductId = Defaults::PRODUCT_SHIPPING_BE_EU_4952;
                        break;
                    case Zone::GLOBAL:
                        $defaultProductId = Defaults::PRODUCT_SHIPPING_BE_GLOBAL_4945;
                        break;
                }
                break;
        }

        if (empty($defaultProductId)) {
            try {
                // TODO Unique exception
                throw new \Exception('No default product available');
            } catch (\Throwable $e) {
                //TODO warn here
                throw $e;
            }
        }

        return $defaultProductId;
    }

    /**
     * @param string $destinationZone
     * @param string $deliveryType
     * @return string[]
     */
    private function requiredFlags(
        string $destinationZone,
        string $deliveryType
    ): array
    {
        if (in_array($destinationZone, [Zone::EU, Zone::GLOBAL])) {
            return [];
        }

        switch ($deliveryType) {
            case DeliveryType::SHIPMENT:
                return [
                    ProductDefinition::PROP_HOME_ALONE,
                    ProductDefinition::PROP_RETURN_IF_NOT_HOME,
                    ProductDefinition::PROP_INSURANCE,
                    ProductDefinition::PROP_SIGNATURE,
//                    ProductDefinition::PROP_AGE_CHECK,
                ];
            case DeliveryType::PICKUP:
                return [
                    ProductDefinition::PROP_INSURANCE,
                    ProductDefinition::PROP_SIGNATURE,
//                    ProductDefinition::PROP_AGE_CHECK,
                    ProductDefinition::PROP_NOTIFICATION,
                ];
            case DeliveryType::MAILBOX:
                return [];
        }

        return [];
    }
}
