<?php

namespace PostNL\Shipments\Service\PostNL\Product;

use PostNL\Shipments\Defaults;
use PostNL\Shipments\Entity\Product\ProductCollection;
use PostNL\Shipments\Entity\Product\ProductDefinition;
use PostNL\Shipments\Entity\Product\ProductEntity;
use PostNL\Shipments\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shipments\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shipments\Struct\ProductFlagStruct;
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
        // TODO Log

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
        // TODO Log

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
     * Does the source zone have products? There are only two source zones, NL and BE,
     * but in v1 there are no products for BE yet.
     *
     * @param string $sourceZone
     * @param Context $context
     * @return bool
     */
    public function sourceZoneHasProducts(string $sourceZone, Context $context): bool
    {
        // TODO Log
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
     * @return array
     * @throws \Exception
     */
    public function getAvailableDeliveryTypes(
        string  $sourceZone,
        string  $destinationZone,
        Context $context
    ): array
    {
        // TODO Log
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
    public function getDefaultProductForConfiguration(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        Context $context
    ): ProductEntity
    {
        $this->logger->debug('Getting default product', [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
        ]);

        $defaultProductId = null;

        switch ($destinationZone) {
            case Zone::NL:
                if ($sourceZone == Zone::NL) {
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
                }
                break;
            case Zone::BE:
                switch ($sourceZone) {
                    case Zone::NL:
                        switch ($deliveryType) {
                            case DeliveryType::SHIPMENT:
                                $defaultProductId = Defaults::PRODUCT_SHIPPING_NL_BE;
                                break;
                            case DeliveryType::PICKUP:
                                $defaultProductId = Defaults::PRODUCT_PICKUP_NL_BE;
                                break;
                        }
                        break;
                    case Zone::BE:
                        // Nothing available yet.
                        break;
                }
                break;
            case Zone::EU:
                $defaultProductId = Defaults::PRODUCT_SHIPPING_EU_4952;
                break;
            case Zone::GLOBAL:
                $defaultProductId = Defaults::PRODUCT_SHIPPING_GLOBAL_4947;
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

        try {
            return $this->getProduct($defaultProductId, $context);
        } catch (\Exception $e) {
            //TODO Unique exception
            throw new \Exception('Could not find default product');
        }
    }

    public function getProductForConfiguration(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array   $flags,
        Context $context
    ): ProductEntity
    {
        $this->logger->debug("Selecting PostNL product", [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
            'flags' => $flags,
        ]);

        $requiredFlags = $this->requiredFlags($destinationZone, $deliveryType);

        foreach (ProductDefinition::ALL_FLAGS as $flag) {
            $isInFlags = in_array($flag, array_keys($flags));
            $isRequired = in_array($flag, $requiredFlags);

            if ($isInFlags && $isRequired) {
                continue;
            }

            if (!$isInFlags && $isRequired) {
                $flags[$flag] = false;
                continue;
            }

            $flags[$flag] = null;
        }

        $products = $this->getProductsForConfiguration($sourceZone, $destinationZone, $deliveryType, $flags, $context);

        if ($products->count() === 1) {
            return $products->first();
        }

        throw new \Exception("Could not select product");
    }

    public function getProductsForConfiguration(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array   $flags,
        Context $context
    ): ProductCollection
    {
        $this->logger->debug("Getting PostNL products", [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
            'flags' => $flags,
        ]);

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('sourceZone', $sourceZone),
            new EqualsFilter('destinationZone', $destinationZone),
            new EqualsFilter('deliveryType', $deliveryType)
        );

        foreach ($flags as $flag => $value) {
            if (!in_array($flag, $this->requiredFlags($destinationZone, $deliveryType))) {
                continue;
            }

            $criteria->addFilter(new EqualsFilter($flag, $value));
        }

        return $this->getProducts($criteria, $context);
    }

    /**
     * @param string $sourceZone
     * @param string $destinationZone
     * @param string $deliveryType
     * @param array $flags
     * @param Context $context
     * @return ProductFlagStruct[]
     * @throws \Exception
     */
    public function getFlags(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array   $flags,
        Context $context
    ): array
    {
        $this->logger->debug("Get available product options", [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
            'options' => $flags,
        ]);

        $requiredFlags = $this->requiredFlags($destinationZone, $deliveryType);

        if (empty($requiredFlags)) {
            return [];
        }

        $availableProducts = $this->getProductsForConfiguration($sourceZone, $destinationZone, $deliveryType, $flags, $context);

        $structs = [];

        /**
         * An option should be:
         * - visible
         *  - if it is required.
         *
         * - disabled
         *  - if it is not in $options and,
         *  - if there is only one possible value to select.
         *
         * - selected
         *  - if it is in $options, and the option value equates to true, or
         *  - if the option is disabled, and the only available value equates to true, or
         *  - if when none of the above, when the default value equates to true.
         */


        foreach (ProductDefinition::ALL_FLAGS as $flag) {
            $flagValuesInAvailableProducts = $availableProducts->reduceToProperty($flag);

            $shouldBeVisible = in_array($flag, $requiredFlags);
            $shouldBeDisabled = count($flagValuesInAvailableProducts) == 1;
            $shouldBeSelected = $shouldBeDisabled && $flagValuesInAvailableProducts[0];

            $isInFlags = array_key_exists($flag, $flags);
            $isSelected = $isInFlags && $flags[$flag];
            $isDisabled = !$isInFlags && $shouldBeDisabled;

            $structs[$flag] = new ProductFlagStruct(
                $flag,
                $shouldBeVisible,
                $isDisabled,
                $isSelected || $shouldBeSelected
            );
        }

        return $structs;
    }

    /**
     * @param string $destinationZone
     * @param string $deliveryType
     * @return array|string[]
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
