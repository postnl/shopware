<?php

namespace PostNL\Shipments\Struct;

use PostNL\Shipments\Service\Attribute\EntityAttributeStruct;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;

class ShippingMethodStruct extends EntityAttributeStruct
{
    /**
     * @inheritDoc
     */
    public function supports(): string
    {
        return ShippingMethodEntity::class;
    }

    /**
     * @var string|null
     */
    protected $deliveryType;

    /**
     * @return string|null
     */
    public function getDeliveryType(): ?string
    {
        return $this->deliveryType;
    }
}
