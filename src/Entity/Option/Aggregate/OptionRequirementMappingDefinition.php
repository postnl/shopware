<?php declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Option;

use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class OptionRequirementMappingDefinition extends MappingEntityDefinition
{
    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return 'postnl_option_requirement';
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('option_id', 'OptionId', OptionDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),
            (new FkField('required_id', 'requiredId', OptionDefinition::class))
                ->addFlags(new PrimaryKey(), new Required()),

            (new ManyToOneAssociationField(
                'productOption',
                'product_option_id',
                OptionDefinition::class
            ))->addFlags(new CascadeDelete()),

            (new ManyToOneAssociationField(
                'required',
                'required_id',
                OptionDefinition::class
            ))->addFlags(new CascadeDelete()),
        ]);
    }
}
