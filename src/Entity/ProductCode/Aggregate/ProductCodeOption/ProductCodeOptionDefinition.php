<?php

namespace PostNL\Shipments\Entity\ProductCode\Aggregate\ProductCodeOption;

use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductOption\ProductOptionDefinition;
use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
class ProductCodeOptionDefinition extends MappingEntityDefinition
{
    public function getEntityName() : string
    {
        return 'postnl_shipments_product_code_option';
    }
    protected function defineFields() : FieldCollection
    {
        return new FieldCollection([
            (new FkField('product_option_id', 'productOptionId', ProductOptionDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),
            (new FkField('product_code_config_id', 'productCodeConfigId', ProductCodeConfigDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),

            (new ManyToOneAssociationField(
                'productOption',
                'product_option_id',
                ProductOptionDefinition::class
            ))->addFlags(new CascadeDelete()),
            (new ManyToOneAssociationField(
                'productCodeConfig',
                'product_code_config_id',
                ProductCodeConfigDefinition::class
            ))->addFlags(new CascadeDelete()),
            new CreatedAtField()
        ]);
    }
}
