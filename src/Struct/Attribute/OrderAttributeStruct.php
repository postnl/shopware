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

    protected ?string $productId = null;
    protected ?string $barCode = null;
    protected ?int $pickupPointLocationCode = null;
    protected ?bool $confirm = null;
    protected ?array $timeframe = null;
    protected ?OrderReturnAttributeStruct $returns = null;

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function getBarCode(): ?string
    {
        return $this->barCode;
    }

    public function getPickupPointLocationCode(): ?int
    {
        return $this->pickupPointLocationCode;
    }

    public function getConfirm(): ?bool
    {
        return $this->confirm;
    }

    public function getTimeframe(): ?array
    {
        return $this->timeframe;
    }

    public function getReturns(): ?OrderReturnAttributeStruct
    {
        return $this->returns;
    }
}
