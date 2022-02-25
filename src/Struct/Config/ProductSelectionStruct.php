<?php

namespace PostNL\Shipments\Struct\Config;

use PostNL\Shipments\Service\Attribute\AttributeStruct;

class ProductSelectionStruct extends AttributeStruct
{
    /** @var bool */
    protected $enabled;

    /** @var int */
    protected $cartAmount;

    /** @var string */
    protected $productId;

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return int
     */
    public function getCartAmount(): int
    {
        return $this->cartAmount;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

}
