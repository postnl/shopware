<?php

declare(strict_types=1);

namespace PostNl\Shipments\Entity\ProductCode\Aggregate\ProductOptionTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class ProductOptionTranslationEntity extends TranslationEntity
{
    use EntityIdTrait;

    /** @var string */
    protected $name;

    public function setName(string $value): void
    {
        $this->name = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
