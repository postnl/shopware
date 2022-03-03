<?php

namespace PostNL\Shopware6\Struct;

use PostNL\Shopware6\Service\Attribute\EntityAttributeStruct;
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
