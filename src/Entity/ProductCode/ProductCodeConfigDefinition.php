<?php

declare(strict_types=1);

namespace PostNl\Shipments\Entity\ProductCode;

use PostNl\Shipments\Entity\ProductCode\Aggregate\ProductCodeConfigTranslation\ProductCodeConfigTranslationDefinition;
use PostNl\Shipments\Entity\ProductCode\Aggregate\ProductCodeOption\ProductCodeOptionDefinition;
use PostNl\Shipments\Entity\ProductCode\Aggregate\ProductOption\ProductOptionDefinition;
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

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductCodeConfigEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductCodeConfigCollection::class;
    }

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

            new BoolField('next_door_delivery', 'nextDoorDelivery'),
            new BoolField('return_if_not_home', 'returnIfNotHome'),
            new BoolField('insurance', 'insurance'),
            new BoolField('signature', 'signature'),
            new BoolField('age_check', 'ageCheck'),
            new BoolField('notification', 'notification'),

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
