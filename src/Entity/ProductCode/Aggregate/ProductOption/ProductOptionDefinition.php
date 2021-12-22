<?php

declare(strict_types=1);

namespace PostNl\Shipments\Entity\ProductCode\Aggregate\ProductOption;

use PostNl\Shipments\Entity\ProductCode\Aggregate\ProductCodeOption\ProductCodeOptionDefinition;
use PostNl\Shipments\Entity\ProductCode\Aggregate\ProductOptionTranslation\ProductOptionTranslationDefinition;
use PostNl\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
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
    public function getEntityName(): string
    {
        return 'postnl_shipments_product_option';
    }

    public function getEntityClass(): string
    {
        return ProductOptionEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductOptionCollection::class;
    }

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
