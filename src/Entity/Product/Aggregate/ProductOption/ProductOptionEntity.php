<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Product\Aggregate\ProductOption;

use Firstred\PostNL\Entity\ProductOption;
use PostNL\Shopware6\Entity\Product\Aggregate\ProductOptionTranslation\ProductOptionTranslationCollection;
use PostNL\Shopware6\Entity\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductOptionEntity extends Entity
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

    /** @var ProductOptionTranslationCollection */
    protected $translations;

    /** @var ProductDefinition|null */
    protected $products;

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
     * @return ProductOptionTranslationCollection
     */
    public function getTranslations(): ProductOptionTranslationCollection
    {
        return $this->translations;
    }

    /**
     * @param ProductOptionTranslationCollection $translations
     */
    public function setTranslations(ProductOptionTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    /**
     * @return ProductDefinition|null
     */
    public function getProducts(): ?ProductDefinition
    {
        return $this->products;
    }

    /**
     * @param ProductDefinition|null $products
     */
    public function setProducts(?ProductDefinition $products): void
    {
        $this->products = $products;
    }

    public function getApiEntity(): ProductOption
    {
        return new ProductOption($this->characteristic, $this->option);
    }
}
