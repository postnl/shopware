<?php declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Option;

use PostNL\Shopware6\Entity\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class ProductOptionRequiredMappingDefinition extends MappingEntityDefinition
{
    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return 'postnl_product_option_required_mapping';
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('product_id', 'productId', ProductDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),
            (new FkField('option_id', 'optionId', OptionDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),

            (new ManyToOneAssociationField(
                'product',
                'product_id',
                ProductDefinition::class
            ))->addFlags(new CascadeDelete()),

            (new ManyToOneAssociationField(
                'option',
                'option_id',
                OptionDefinition::class
            ))->addFlags(new CascadeDelete()),
        ]);
    }
}
