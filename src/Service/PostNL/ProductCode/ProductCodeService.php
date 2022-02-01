<?php

namespace PostNL\Shipments\Service\PostNL\ProductCode;

use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigCollection;
use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigEntity;
use PostNL\Shipments\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shipments\Service\PostNL\Delivery\Zone\Zone;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class ProductCodeService
{

    /**
     * @var EntityRepositoryInterface
     */
    protected $productCodeRepository;

    public function __construct(
        EntityRepositoryInterface $productCodeRepository
    )
    {
        $this->productCodeRepository = $productCodeRepository;
    }

    public function getProduct(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        array   $options,
        Context $context
    ): ProductCodeConfigEntity
    {
        $allOptions = ['nextDoorDelivery', 'returnIfNotHome', 'insurance', 'signature', 'ageCheck', 'notification'];
        foreach ($allOptions as $option) {
            $isInOptions = in_array($option, array_keys($options));
            $isRequired = in_array($option, $this->requiredOptions($destinationZone, $deliveryType));

            if ($isInOptions && $isRequired) {
                continue;
            }

            if(!$isInOptions && $isRequired) {
                $options[$option] = false;
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
                return ['nextDoorDelivery', 'returnIfNotHome', 'insurance', 'signature', 'ageCheck'];
            case DeliveryType::PICKUP:
                return ['insurance', 'signature', 'ageCheck', 'notification'];
            case DeliveryType::MAILBOX:
                return [];
        }

        return [];
    }
}
