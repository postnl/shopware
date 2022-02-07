<?php

namespace PostNL\Shipments\Service\PostNL\ProductCode;

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
            new EqualsFilter('deliveryType', $deliveryType),
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
     * @return array
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
