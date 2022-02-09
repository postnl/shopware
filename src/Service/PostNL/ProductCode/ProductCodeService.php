<?php

namespace PostNL\Shipments\Service\PostNL\ProductCode;

use PostNL\Shipments\Defaults;
use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigCollection;
use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigEntity;
use PostNL\Shipments\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shipments\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shipments\Struct\ProductCodeOptionStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductCodeService
{
    const ALL_OPTS = [
        ProductCodeConfigDefinition::OPT_NEXT_DOOR_DELIVERY,
        ProductCodeConfigDefinition::OPT_RETURN_IF_NOT_HOME,
        ProductCodeConfigDefinition::OPT_INSURANCE,
        ProductCodeConfigDefinition::OPT_SIGNATURE,
        ProductCodeConfigDefinition::OPT_AGE_CHECK,
        ProductCodeConfigDefinition::OPT_NOTIFICATION,
    ];

    /**
     * @var EntityRepositoryInterface
     */
    protected $productCodeRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        EntityRepositoryInterface $productCodeRepository,
        LoggerInterface $logger
    )
    {
        $this->productCodeRepository = $productCodeRepository;
        $this->logger = $logger;
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
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('sourceZone', $sourceZone));

        /** @var ProductCodeConfigCollection $products */
        $products = $this->productCodeRepository->search($criteria, $context)->getEntities();

        return $products->count() > 0;
    }

    /**
     * @param string $sourceZone
     * @param string $destinationZone
     * @param Context $context
     * @return array
     * @throws \Exception
     */
    public function getAvailableDeliveryTypes(
        string $sourceZone,
        string $destinationZone,
        Context $context
    ): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('sourceZone', $sourceZone),
            new EqualsFilter('destinationZone', $destinationZone)
        );

        /** @var ProductCodeConfigCollection $products */
        $products = $this->productCodeRepository->search($criteria, $context)->getEntities();

        if($products->count() > 0) {
            return array_values($products->reduceToProperty('deliveryType'));
        }

        throw new \Exception('No products found for this zone combination');
    }

    public function getDefaultProduct(
        string $sourceZone,
        string $destinationZone,
        string $deliveryType,
        Context $context
    ): ProductCodeConfigEntity
    {
        $this->logger->debug('Getting default product', [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
        ]);

        $defaultProductId = null;

        switch($destinationZone) {
            case Zone::NL:
                if($sourceZone == Zone::NL) {
                    switch($deliveryType) {
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
                switch($sourceZone) {
                    case Zone::NL:
                        switch($deliveryType) {
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

        if(empty($defaultProductId)) {
            try {
                throw new \Exception('No default product available');
            } catch(\Throwable $e) {
                //warn here
                throw $e;
            }
        }

        $product = $this->productCodeRepository->search(new Criteria([$defaultProductId]), $context)->first();

        if($product instanceof ProductCodeConfigEntity) {
           return $product;
        }

        throw new \Exception('Could not find default product');
    }


    public function getProduct(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array   $options,
        Context $context
    ): ProductCodeConfigEntity
    {
        $this->logger->debug("Selecting PostNL product", [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
            'options' => $options,
        ]);

        $requiredOptions = $this->requiredOptions($destinationZone, $deliveryType);

        foreach (self::ALL_OPTS as $option) {
            $isInOptions = in_array($option, array_keys($options));
            $isRequired = in_array($option, $requiredOptions);

            if ($isInOptions && $isRequired) {
                continue;
            }

            if (!$isInOptions && $isRequired) {
                $options[$option] = $this->getDefaultOptionValue($option);
                continue;
            }

            $options[$option] = null;
        }

        $products = $this->getProducts($sourceZone, $destinationZone, $deliveryType, $options, $context);

        if ($products->count() === 1) {
            return $products->first();
        }

        throw new \Exception("Could not select product");
    }

    public function getProducts(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array   $options,
        Context $context
    ): ProductCodeConfigCollection
    {
        $this->logger->debug("Getting PostNL products", [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
            'options' => $options,
        ]);

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('sourceZone', $sourceZone),
            new EqualsFilter('destinationZone', $destinationZone),
            new EqualsFilter('deliveryType', $deliveryType)
        );

        foreach ($options as $option => $value) {
            if (!in_array($option, $this->requiredOptions($destinationZone, $deliveryType))) {
                continue;
            }

            $criteria->addFilter(new EqualsFilter($option, $value));
        }

        /** @var ProductCodeConfigCollection $products */
        $products = $this->productCodeRepository->search($criteria, $context)->getEntities();

        if ($products->count() > 0) {
            return $products;
        }

        throw new \Exception('Could not find valid products');
    }

    /**
     * @param string $sourceZone
     * @param string $destinationZone
     * @param string $deliveryType
     * @param array $options
     * @param Context $context
     * @return ProductCodeOptionStruct[]
     * @throws \Exception
     */
    public function getOptions(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array   $options,
        Context $context
    ): array
    {
        $this->logger->debug("Get available product options", [
            'sourceZone' => $sourceZone,
            'destinationZone' => $destinationZone,
            'deliveryType' => $deliveryType,
            'options' => $options,
        ]);

        $availableProducts = $this->getProducts($sourceZone, $destinationZone, $deliveryType, $options, $context);
        $requiredOptions = $this->requiredOptions($destinationZone, $deliveryType);

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


        foreach (self::ALL_OPTS as $option) {
            $optionValuesInAvailableProducts = $availableProducts->reduceToProperty($option);

            $shouldBeVisible = in_array($option, $requiredOptions);
            $shouldBeDisabled = count($optionValuesInAvailableProducts) == 1;
            $shouldBeSelected = $shouldBeDisabled
                ? $optionValuesInAvailableProducts[0]
                : $this->getDefaultOptionValue($option);

            $isInOptions = array_key_exists($option, $options);
            $isSelected = $isInOptions && $options[$option];
            $isDisabled = !$isInOptions && $shouldBeDisabled;

            $structs[$option] = new ProductCodeOptionStruct(
                $option,
                $shouldBeVisible,
                $isDisabled,
                $isSelected || $shouldBeSelected
            );
        }

        return $structs;
    }

    /**
     * @param string $option
     * @return bool
     */
    public function getDefaultOptionValue(string $option): bool
    {
        if(in_array($option, [
            ProductCodeConfigDefinition::OPT_NEXT_DOOR_DELIVERY
        ])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $destinationZone
     * @param string $deliveryType
     * @return array|string[]
     */
    private function requiredOptions(
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
                    ProductCodeConfigDefinition::OPT_NEXT_DOOR_DELIVERY,
                    ProductCodeConfigDefinition::OPT_RETURN_IF_NOT_HOME,
                    ProductCodeConfigDefinition::OPT_INSURANCE,
                    ProductCodeConfigDefinition::OPT_SIGNATURE,
                    ProductCodeConfigDefinition::OPT_AGE_CHECK,
                ];
            case DeliveryType::PICKUP:
                return [
                    ProductCodeConfigDefinition::OPT_INSURANCE,
                    ProductCodeConfigDefinition::OPT_SIGNATURE,
                    ProductCodeConfigDefinition::OPT_AGE_CHECK,
                    ProductCodeConfigDefinition::OPT_NOTIFICATION,
                ];
            case DeliveryType::MAILBOX:
                return [];
        }

        return [];
    }
}
