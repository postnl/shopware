<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\ProductCode;

use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductCodeConfigTranslation\ProductCodeConfigTranslationDefinition;
use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductCodeOption\ProductCodeOptionDefinition;
use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductOption\ProductOptionDefinition;
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

class ProductCodeConfigDefinition extends EntityDefinition
{
    const ENTITY_NAME = 'postnl_shipments_product_code_config';

    const OPT_NEXT_DOOR_DELIVERY = 'nextDoorDelivery';
    const OPT_RETURN_IF_NOT_HOME = 'returnIfNotHome';
    const OPT_INSURANCE = 'insurance';
    const OPT_SIGNATURE = 'signature';
    const OPT_AGE_CHECK = 'ageCheck';
    const OPT_NOTIFICATION = 'notification';

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
        return ProductCodeConfigEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return ProductCodeConfigCollection::class;
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
            (new StringField('product_code_delivery', 'productCodeDelivery', 255))
                ->addFlags(new Required()),
            (new StringField('source_zone', 'sourceZone', 255))
                ->addFlags(new Required()),
            (new StringField('destination_zone', 'destinationZone', 255))
                ->addFlags(new Required()),
            (new StringField('delivery_type', 'deliveryType', 255))
                ->addFlags(new Required()),

            new BoolField('next_door_delivery', self::OPT_NEXT_DOOR_DELIVERY),
            new BoolField('return_if_not_home', self::OPT_RETURN_IF_NOT_HOME),
            new BoolField('insurance', self::OPT_INSURANCE),
            new BoolField('signature', self::OPT_SIGNATURE),
            new BoolField('age_check', self::OPT_AGE_CHECK),
            new BoolField('notification', self::OPT_NOTIFICATION),

            new TranslationsAssociationField(ProductCodeConfigTranslationDefinition::class, 'product_code_config_id'),

            new ManyToManyAssociationField(
                'productOptions',
                ProductOptionDefinition::class,
                ProductCodeOptionDefinition::class,
                'product_code_config_id',
                'product_option_id'
            ),
        ]);
    }
}
