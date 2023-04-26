<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Option\Aggregate\OptionTranslation;

use PostNL\Shopware6\Entity\Option\OptionDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class OptionTranslationDefinition extends EntityTranslationDefinition
{
    const ENTITY_NAME = 'postnl_option_translation';

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
        return OptionTranslationEntity::class;
    }

    /**
     * @return string
     */
    public function getCollectionClass(): string
    {
        return OptionTranslationCollection::class;
    }

    /**
     * @return string
     */
    public function getParentDefinitionClass(): string
    {
        return OptionDefinition::class;
    }

    /**
     * @return FieldCollection
     */
    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name', 255))
                ->addFlags(new Required()),
            (new StringField('description', 'description', 255))
        ]);
    }
}
