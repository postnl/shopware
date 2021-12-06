<?php

declare(strict_types=1);

namespace PostNl\Shipments\Entity\ProductCode\Aggregate\ProductOption;

use Firstred\PostNL\Entity\ProductOption;
use PostNl\Shipments\Entity\ProductCode\Aggregate\ProductOptionTranslation\ProductOptionTranslationCollection;
use PostNl\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ProductOptionEntity extends Entity
{
    use EntityIdTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $characteristic;

    /** @var string */
    protected $option;

    /** @var ProductOptionTranslationCollection */
    protected $translations;

    /** @var ProductCodeConfigDefinition|null */
    protected $productCodes;

    public function setName(?string $value): void
    {
        $this->name = $value;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setCharacteristic(string $value): void
    {
        $this->characteristic = $value;
    }

    public function getCharacteristic(): string
    {
        return $this->characteristic;
    }

    public function setOption(string $value): void
    {
        $this->option = $value;
    }

    public function getOption(): string
    {
        return $this->option;
    }

    public function setTranslations(?ProductOptionTranslationCollection $value): void
    {
        $this->translations = $value;
    }

    public function getTranslations(): ?ProductOptionTranslationCollection
    {
        return $this->translations;
    }

    public function setProductCodes(?ProductCodeConfigDefinition $value): void
    {
        $this->productCodes = $value;
    }

    public function getProductCodes(): ?ProductCodeConfigDefinition
    {
        return $this->productCodes;
    }

    public function getApiEntity(): ProductOption
    {
        return new ProductOption($this->characteristic, $this->option);
    }
}
