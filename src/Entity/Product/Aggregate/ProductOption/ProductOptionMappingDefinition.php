<?php

namespace PostNL\Shipments\Entity\Product\Aggregate\ProductOption;

use PostNL\Shipments\Entity\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class ProductOptionMappingDefinition extends MappingEntityDefinition
{
    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return 'postnl_shipments_product_option_mapping';
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('product_option_id', 'productOptionId', ProductOptionDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),
            (new FkField('product_id', 'productId', ProductDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),

            (new ManyToOneAssociationField(
                'productOption',
                'product_option_id',
                ProductOptionDefinition::class
            ))->addFlags(new CascadeDelete()),

            (new ManyToOneAssociationField(
                'product',
                'product_id',
                ProductDefinition::class
            ))->addFlags(new CascadeDelete()),

            new CreatedAtField()
        ]);
    }
}
