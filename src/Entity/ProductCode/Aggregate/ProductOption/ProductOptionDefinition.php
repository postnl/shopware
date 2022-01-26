<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\ProductCode\Aggregate\ProductOption;

use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductCodeOption\ProductCodeOptionDefinition;
use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductOptionTranslation\ProductOptionTranslationDefinition;
use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductOptionDefinition extends EntityDefinition
{
    const ENTITY_NAME = 'postnl_shipments_product_option';

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
        return ProductOptionEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return ProductOptionCollection::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))
                ->addFlags(new Required(), new PrimaryKey()),
            new TranslatedField('name'),
            (new StringField('characteristic', 'characteristic', 3))
                ->addFlags(new Required()),
            (new StringField('option', 'option', 3))
                ->addFlags(new Required()),

            new TranslationsAssociationField(ProductOptionTranslationDefinition::class, 'product_option_id'),

            new ManyToManyAssociationField(
                'productCodes',
                ProductCodeConfigDefinition::class,
                ProductCodeOptionDefinition::class,
                'product_option_id',
                'product_code_config_id'),
        ]);
    }
}
