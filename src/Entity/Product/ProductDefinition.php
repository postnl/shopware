<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Product;

use PostNL\Shopware6\Entity\Option\OptionDefinition;
use PostNL\Shopware6\Entity\Option\ProductOptionOptionalMappingDefinition;
use PostNL\Shopware6\Entity\Option\ProductOptionRequiredMappingDefinition;
use PostNL\Shopware6\Entity\Product\Aggregate\ProductTranslation\ProductTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductDefinition extends EntityDefinition
{
    const ENTITY_NAME = 'postnl_product';

    const STOR_HOME_ALONE         = 'home_alone';
    const STOR_RETURN_IF_NOT_HOME = 'return_if_not_home';
    const STOR_INSURANCE          = 'insurance';
    const STOR_SIGNATURE          = 'signature';
    const STOR_AGE_CHECK          = 'age_check';
    const STOR_NOTIFICATION       = 'notification';

    const PROP_HOME_ALONE         = 'homeAlone';
    const PROP_RETURN_IF_NOT_HOME = 'returnIfNotHome';
    const PROP_INSURANCE          = 'insurance';
    const PROP_SIGNATURE          = 'signature';
    const PROP_AGE_CHECK          = 'ageCheck';
    const PROP_NOTIFICATION       = 'notification';

    const ALL_FLAGS = [
        self::STOR_HOME_ALONE         => self::PROP_HOME_ALONE,
        self::STOR_RETURN_IF_NOT_HOME => self::PROP_RETURN_IF_NOT_HOME,
        self::STOR_INSURANCE          => self::PROP_INSURANCE,
        self::STOR_SIGNATURE          => self::PROP_SIGNATURE,
        self::STOR_AGE_CHECK          => self::PROP_AGE_CHECK,
        self::STOR_NOTIFICATION       => self::PROP_NOTIFICATION,
    ];

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
        return ProductEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return ProductCollection::class;
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
            new TranslatedField('description'),
            (new StringField('product_code_delivery', 'productCodeDelivery', 255))
                ->addFlags(new Required()),
            (new StringField('source_zone', 'sourceZone', 255))
                ->addFlags(new Required()),
            (new StringField('destination_zone', 'destinationZone', 255))
                ->addFlags(new Required()),
            (new StringField('delivery_type', 'deliveryType', 255))
                ->addFlags(new Required()),

            new BoolField(self::STOR_HOME_ALONE, self::PROP_HOME_ALONE),
            new BoolField(self::STOR_RETURN_IF_NOT_HOME, self::PROP_RETURN_IF_NOT_HOME),
            new BoolField(self::STOR_INSURANCE, self::PROP_INSURANCE),
            new BoolField(self::STOR_SIGNATURE, self::PROP_SIGNATURE),
            new BoolField(self::STOR_AGE_CHECK, self::PROP_AGE_CHECK),
            new BoolField(self::STOR_NOTIFICATION, self::PROP_NOTIFICATION),

            new TranslationsAssociationField(ProductTranslationDefinition::class, 'product_id'),

            new ManyToManyAssociationField(
                'requiredOptions',
                OptionDefinition::class,
                ProductOptionRequiredMappingDefinition::class,
                'product_id',
                'option_id'
            ),
            new ManyToManyAssociationField(
                'optionalOptions',
                OptionDefinition::class,
                ProductOptionOptionalMappingDefinition::class,
                'product_id',
                'option_id'
            ),
        ]);
    }
}
