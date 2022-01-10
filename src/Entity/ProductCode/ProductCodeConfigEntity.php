<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\ProductCode;

use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductCodeConfigTranslation\ProductCodeConfigTranslationCollection;
use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductOption\ProductOptionCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductCodeConfigEntity extends Entity
{
    use EntityIdTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $productCodeDelivery;

    /** @var ProductOptionCollection */
    protected $productOptions;

    /** @var string */
    protected $sourceZone;

    /** @var string */
    protected $destinationZone;

    /** @var string */
    protected $deliveryType;

    /** @var bool|null */
    protected $nextDoorDelivery;

    /** @var bool|null */
    protected $returnIfNotHome;

    /** @var bool|null */
    protected $insurance;

    /** @var bool|null */
    protected $signature;

    /** @var bool|null */
    protected $ageCheck;

    /** @var bool|null */
    protected $notification;

    /** @var ProductCodeConfigTranslationCollection */
    protected $translations;

    public function setName(?string $value): void
    {
        $this->name = $value;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setProductCodeDelivery(string $value): void
    {
        $this->productCodeDelivery = $value;
    }

    public function getProductCodeDelivery(): string
    {
        return $this->productCodeDelivery;
    }

    public function setSourceZone(string $value): void
    {
        $this->sourceZone = $value;
    }

    public function getSourceZone(): string
    {
        return $this->sourceZone;
    }

    public function setDestinationZone(string $value): void
    {
        $this->destinationZone = $value;
    }

    public function getDestinationZone(): string
    {
        return $this->destinationZone;
    }

    public function setDeliveryType(string $value): void
    {
        $this->deliveryType = $value;
    }

    public function getDeliveryType(): string
    {
        return $this->deliveryType;
    }

    public function setNextDoorDelivery(?bool $value): void
    {
        $this->nextDoorDelivery = $value;
    }

    public function getNextDoorDelivery(): ?bool
    {
        return $this->nextDoorDelivery;
    }

    public function setReturnIfNotHome(?bool $value): void
    {
        $this->returnIfNotHome = $value;
    }

    public function getReturnIfNotHome(): ?bool
    {
        return $this->returnIfNotHome;
    }

    public function setInsurance(?bool $value): void
    {
        $this->insurance = $value;
    }

    public function getInsurance(): ?bool
    {
        return $this->insurance;
    }

    public function setSignature(?bool $value): void
    {
        $this->signature = $value;
    }

    public function getSignature(): ?bool
    {
        return $this->signature;
    }

    public function setAgeCheck(?bool $value): void
    {
        $this->ageCheck = $value;
    }

    public function getAgeCheck(): ?bool
    {
        return $this->ageCheck;
    }

    public function setNotification(?bool $value): void
    {
        $this->notification = $value;
    }

    public function getNotification(): ?bool
    {
        return $this->notification;
    }

    public function setTranslations(?ProductCodeConfigTranslationCollection $value): void
    {
        $this->translations = $value;
    }

    public function getTranslations(): ?ProductCodeConfigTranslationCollection
    {
        return $this->translations;
    }
}
