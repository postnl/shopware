<?php declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Attribute;

use PostNL\Shopware6\Service\Attribute\EntityAttributeStruct;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;

class ShippingMethodAttributeStruct extends EntityAttributeStruct
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
