<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Product\Aggregate\ProductTranslation;

use PostNL\Shopware6\Entity\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductTranslationDefinition extends EntityTranslationDefinition
{
    const ENTITY_NAME = 'postnl_product_translation';

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return ProductTranslationEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return ProductTranslationCollection::class;
    }

    /**
     * @return string
     */
    public function getParentDefinitionClass(): string
    {
        return ProductDefinition::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name', 255))
                ->addFlags(new Required()),
            (new StringField('description', 'description', 255))
        ]);
    }
}
