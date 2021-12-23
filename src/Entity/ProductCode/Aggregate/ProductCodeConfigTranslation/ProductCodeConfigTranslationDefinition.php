<?php

declare(strict_types=1);

namespace PostNl\Shipments\Entity\ProductCode\Aggregate\ProductCodeConfigTranslation;

use PostNl\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductCodeConfigTranslationDefinition extends EntityTranslationDefinition
{
    const ENTITY_NAME = 'postnl_shipments_product_code_config_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductCodeConfigTranslationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductCodeConfigTranslationCollection::class;
    }

    public function getParentDefinitionClass(): string
    {
        return ProductCodeConfigDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name', 255))
                ->addFlags(new Required())
        ]);
    }
}
