<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\ProductCode\Aggregate\ProductOptionTranslation;

use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductOption\ProductOptionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductOptionTranslationDefinition extends EntityTranslationDefinition
{
    const ENTITY_NAME = 'postnl_shipments_product_option_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductOptionTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductOptionTranslationCollection::class;
    }

    public function getParentDefinitionClass(): string
    {
        return ProductOptionDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name', 255))
                ->addFlags(new Required())
        ]);
    }
}
