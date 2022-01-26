<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\ProductCode\Aggregate\ProductCodeConfigTranslation;

use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductCodeConfigTranslationDefinition extends EntityTranslationDefinition
{
    const ENTITY_NAME = 'postnl_shipments_product_code_config_translation';

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
        return ProductCodeConfigTranslationEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return ProductCodeConfigTranslationCollection::class;
    }

    /**
     * @return string
     */
    public function getParentDefinitionClass(): string
    {
        return ProductCodeConfigDefinition::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name', 255))
                ->addFlags(new Required())
        ]);
    }
}
