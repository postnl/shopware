<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Option;

use Firstred\PostNL\Entity\ProductOption;
use PostNL\Shopware6\Entity\Option\Aggregate\OptionTranslation\OptionTranslationCollection;
use PostNL\Shopware6\Entity\Product\ProductCollection;
use PostNL\Shopware6\Entity\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class OptionEntity extends Entity
{
    use EntityIdTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var string */
    protected $characteristic;

    /** @var string */
    protected $option;

    /** @var OptionTranslationCollection */
    protected $translations;

    /** @var ProductCollection */
    protected $requiredByProducts;

    /** @var ProductCollection */
    protected $optionalForProducts;

    /** @var OptionCollection */
    protected $required;

    /** @var OptionCollection */
    protected $requiredBy;

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
    public function getCharacteristic(): string
    {
        return $this->characteristic;
    }

    /**
     * @param string $characteristic
     */
    public function setCharacteristic(string $characteristic): void
    {
        $this->characteristic = $characteristic;
    }

    /**
     * @return string
     */
    public function getOption(): string
    {
        return $this->option;
    }

    /**
     * @param string $option
     */
    public function setOption(string $option): void
    {
        $this->option = $option;
    }

    /**
     * @return OptionTranslationCollection
     */
    public function getTranslations(): OptionTranslationCollection
    {
        return $this->translations;
    }

    /**
     * @param OptionTranslationCollection $translations
     */
    public function setTranslations(OptionTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return ProductCollection
     */
    public function getRequiredByProducts(): ProductCollection
    {
        return $this->requiredByProducts;
    }

    /**
     * @param ProductCollection $requiredByProducts
     */
    public function setRequiredByProducts(ProductCollection $requiredByProducts): void
    {
        $this->requiredByProducts = $requiredByProducts;
    }

    /**
     * @return ProductCollection
     */
    public function getOptionalForProducts(): ProductCollection
    {
        return $this->optionalForProducts;
    }

    /**
     * @param ProductCollection $optionalForProducts
     */
    public function setOptionalForProducts(ProductCollection $optionalForProducts): void
    {
        $this->optionalForProducts = $optionalForProducts;
    }

    /**
     * @return OptionCollection
     */
    public function getRequired(): OptionCollection
    {
        return $this->required;
    }

    /**
     * @param OptionCollection $required
     */
    public function setRequired(OptionCollection $required): void
    {
        $this->required = $required;
    }

    /**
     * @return OptionCollection
     */
    public function getRequiredBy(): OptionCollection
    {
        return $this->requiredBy;
    }

    /**
     * @param OptionCollection $requiredBy
     */
    public function setRequiredBy(OptionCollection $requiredBy): void
    {
        $this->requiredBy = $requiredBy;
    }

    public function getApiEntity(): ProductOption
    {
        return new ProductOption($this->characteristic, $this->option);
    }
}
