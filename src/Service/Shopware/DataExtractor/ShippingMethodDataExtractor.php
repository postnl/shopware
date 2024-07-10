<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\Shopware\DataExtractor;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;

class ShippingMethodDataExtractor
{
    public function extractDeliveryType(ShippingMethodEntity $shippingMethod): ?string
    {
        $technicalName = $shippingMethod->getTechnicalName();

        if (!is_string($technicalName)) {
            return null;
        }

        if (!str_starts_with($technicalName, Defaults::CUSTOM_FIELDS_KEY)) {
            return null;
        }

        $parts = explode('_', $technicalName, 2);
        $deliveryType = array_pop($parts);

        if (in_array($deliveryType, [DeliveryType::SHIPMENT, DeliveryType::PICKUP, DeliveryType::MAILBOX, DeliveryType::PARCEL])) {
            return $deliveryType;
        }

        return null;
    }
}