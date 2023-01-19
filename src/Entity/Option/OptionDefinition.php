<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Option;

use PostNL\Shopware6\Entity\Option\Aggregate\OptionTranslation\OptionTranslationDefinition;
use PostNL\Shopware6\Entity\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class OptionDefinition extends EntityDefinition
{
    const ENTITY_NAME = 'postnl_option';

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
        return OptionEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return OptionCollection::class;
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
            (new StringField('characteristic', 'characteristic', 3))
                ->addFlags(new Required()),
            (new StringField('option', 'option', 3))
                ->addFlags(new Required()),

            new TranslationsAssociationField(OptionTranslationDefinition::class, 'product_option_id'),

            new ManyToManyAssociationField(
                'products',
                ProductDefinition::class,
                OptionMappingDefinition::class,
                'product_option_id',
                'product_id'),

            new ManyToManyAssociationField(
                'required',
                self::class,
                OptionRequirementMappingDefinition::class,
                'product_option_id',
                'required_id'),

            new ManyToManyAssociationField(
                'requiredBy',
                self::class,
                OptionRequirementMappingDefinition::class,
                'required_id',
                'product_option_id'),
        ]);
    }
}
