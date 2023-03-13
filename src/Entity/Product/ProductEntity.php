<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Product;

use PostNL\Shopware6\Entity\Option\OptionCollection;
use PostNL\Shopware6\Entity\Product\Aggregate\ProductTranslation\ProductTranslationCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductEntity extends Entity
{
    use EntityIdTrait;

    /** @var ?string */
    protected $replacedById;

    /** @var ?self */
    protected $replacedBy;

    /** @var ProductCollection */
    protected $replaces;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var string */
    protected $productCodeDelivery;

    /** @var OptionCollection */
    protected $requiredOptions;

    /** @var OptionCollection */
    protected $optionalOptions;

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
    protected $insurancePlus;

    /** @var bool|null */
    protected $signature;

    /** @var bool|null */
    protected $ageCheck;

    /** @var bool|null */
    protected $notification;

    /** @var bool|null */
    protected $trackAndTrace;

    /** @var bool|null */
    protected $mailboxLargerPackage;

    /** @var ProductTranslationCollection */
    protected $translations;

    /**
     * @return string|null
     */
    public function getReplacedById(): ?string
    {
        return $this->replacedById;
    }

    /**
     * @param string|null $replacedById
     */
    public function setReplacedById(?string $replacedById): void
    {
        $this->replacedById = $replacedById;
    }

    /**
     * @return ProductEntity|null
     */
    public function getReplacedBy(): ?ProductEntity
    {
        return $this->replacedBy;
    }

    /**
     * @param ProductEntity|null $replacedBy
     */
    public function setReplacedBy(?ProductEntity $replacedBy): void
    {
        $this->replacedBy = $replacedBy;
    }

    /**
     * @return ProductCollection
     */
    public function getReplaces(): ProductCollection
    {
        return $this->replaces;
    }

    /**
     * @param ProductCollection $replaces
     */
    public function setReplaces(ProductCollection $replaces): void
    {
        $this->replaces = $replaces;
    }

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
     * @return OptionCollection
     */
    public function getRequiredOptions(): OptionCollection
    {
        return $this->requiredOptions;
    }

    /**
     * @param OptionCollection $requiredOptions
     */
    public function setRequiredOptions(OptionCollection $requiredOptions): void
    {
        $this->requiredOptions = $requiredOptions;
    }

    /**
     * @return OptionCollection
     */
    public function getOptionalOptions(): OptionCollection
    {
        return $this->optionalOptions;
    }

    /**
     * @param OptionCollection $optionalOptions
     */
    public function setOptionalOptions(OptionCollection $optionalOptions): void
    {
        $this->optionalOptions = $optionalOptions;
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
    public function getInsurancePlus(): ?bool
    {
        return $this->insurancePlus;
    }

    /**
     * @param bool|null $insurancePlus
     */
    public function setInsurancePlus(?bool $insurancePlus): void
    {
        $this->insurancePlus = $insurancePlus;
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
     * @return bool|null
     */
    public function getTrackAndTrace(): ?bool
    {
        return $this->trackAndTrace;
    }

    /**
     * @param bool|null $trackAndTrace
     */
    public function setTrackAndTrace(?bool $trackAndTrace): void
    {
        $this->trackAndTrace = $trackAndTrace;
    }

    /**
     * @return bool|null
     */
    public function getMailboxLargerPackage(): ?bool
    {
        return $this->mailboxLargerPackage;
    }

    /**
     * @param bool|null $mailboxLargerPackage
     */
    public function setMailboxLargerPackage(?bool $mailboxLargerPackage): void
    {
        $this->mailboxLargerPackage = $mailboxLargerPackage;
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
