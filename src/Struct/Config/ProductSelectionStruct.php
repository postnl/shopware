<?php

namespace PostNL\Shopware6\Struct\Config;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;

class ProductSelectionStruct extends AttributeStruct
{
    /** @var bool */
    protected $enabled;

    /** @var int */
    protected $cartAmount;

    /** @var string */
    protected $id;

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
    public function getId(): string
    {
        return $this->id;
    }

}
