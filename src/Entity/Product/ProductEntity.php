<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Product;

use PostNL\Shopware6\Entity\Product\Aggregate\ProductOption\ProductOptionCollection;
use PostNL\Shopware6\Entity\Product\Aggregate\ProductTranslation\ProductTranslationCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductEntity extends Entity
{
    use EntityIdTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

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
    protected $homeAlone;

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

    /** @var ProductTranslationCollection */
    protected $translations;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getProductCodeDelivery(): string
    {
        return $this->productCodeDelivery;
    }

    /**
     * @param string $productCodeDelivery
     */
    public function setProductCodeDelivery(string $productCodeDelivery): void
    {
        $this->productCodeDelivery = $productCodeDelivery;
    }

    /**
     * @return ProductOptionCollection
     */
    public function getProductOptions(): ProductOptionCollection
    {
        return $this->productOptions;
    }

    /**
     * @param ProductOptionCollection $productOptions
     */
    public function setProductOptions(ProductOptionCollection $productOptions): void
    {
        $this->productOptions = $productOptions;
    }

    /**
     * @return string
     */
    public function getSourceZone(): string
    {
        return $this->sourceZone;
    }

    /**
     * @param string $sourceZone
     */
    public function setSourceZone(string $sourceZone): void
    {
        $this->sourceZone = $sourceZone;
    }

    /**
     * @return string
     */
    public function getDestinationZone(): string
    {
        return $this->destinationZone;
    }

    /**
     * @param string $destinationZone
     */
    public function setDestinationZone(string $destinationZone): void
    {
        $this->destinationZone = $destinationZone;
    }

    /**
     * @return string
     */
    public function getDeliveryType(): string
    {
        return $this->deliveryType;
    }

    /**
     * @param string $deliveryType
     */
    public function setDeliveryType(string $deliveryType): void
    {
        $this->deliveryType = $deliveryType;
    }

    /**
     * @return bool|null
     */
    public function getHomeAlone(): ?bool
    {
        return $this->homeAlone;
    }

    /**
     * @param bool|null $homeAlone
     */
    public function setHomeAlone(?bool $homeAlone): void
    {
        $this->homeAlone = $homeAlone;
    }

    /**
     * @return bool|null
     */
    public function getReturnIfNotHome(): ?bool
    {
        return $this->returnIfNotHome;
    }

    /**
     * @param bool|null $returnIfNotHome
     */
    public function setReturnIfNotHome(?bool $returnIfNotHome): void
    {
        $this->returnIfNotHome = $returnIfNotHome;
    }

    /**
     * @return bool|null
     */
    public function getInsurance(): ?bool
    {
        return $this->insurance;
    }

    /**
     * @param bool|null $insurance
     */
    public function setInsurance(?bool $insurance): void
    {
        $this->insurance = $insurance;
    }

    /**
     * @return bool|null
     */
    public function getSignature(): ?bool
    {
        return $this->signature;
    }

    /**
     * @param bool|null $signature
     */
    public function setSignature(?bool $signature): void
    {
        $this->signature = $signature;
    }

    /**
     * @return bool|null
     */
    public function getAgeCheck(): ?bool
    {
        return $this->ageCheck;
    }

    /**
     * @param bool|null $ageCheck
     */
    public function setAgeCheck(?bool $ageCheck): void
    {
        $this->ageCheck = $ageCheck;
    }

    /**
     * @return bool|null
     */
    public function getNotification(): ?bool
    {
        return $this->notification;
    }

    /**
     * @param bool|null $notification
     */
    public function setNotification(?bool $notification): void
    {
        $this->notification = $notification;
    }

    /**
     * @return ProductTranslationCollection
     */
    public function getTranslations(): ProductTranslationCollection
    {
        return $this->translations;
    }

    /**
     * @param ProductTranslationCollection $translations
     */
    public function setTranslations(ProductTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }
}
