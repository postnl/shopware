<?php declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Attribute;

use PostNL\Shopware6\Entity\Product\ProductEntity;
use PostNL\Shopware6\Service\Attribute\EntityAttributeStruct;
use Shopware\Core\Checkout\Order\OrderEntity;

class OrderAttributeStruct extends EntityAttributeStruct
{
    /**
     * @inheritDoc
     */
    public function supports(): string
    {
        return OrderEntity::class;
    }

    /**
     * @var string|null
     */
    protected $productId;

    /**
     * @var string|null
     */
    protected $barCode;

    /**
     * @var int|null
     */
    protected $pickupPointLocationCode;

    /**
     * @var bool
     */
    protected $confirm;

    /**
     * @return string|null
     */
    public function getProductId(): ?string
    {
        return $this->productId;
    }

    /**
     * @return string|null
     */
    public function getBarCode(): ?string
    {
        return $this->barCode;
    }

    /**
     * @return int|null
     */
    public function getPickupPointLocationCode(): ?int
    {
        return $this->pickupPointLocationCode;
    }

    /**
     * @return bool
     */
    public function isConfirm(): bool
    {
        return $this->confirm;
    }
}
