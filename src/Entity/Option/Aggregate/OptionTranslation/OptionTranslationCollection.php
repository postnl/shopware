<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Option\Aggregate\OptionTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                         add(OptionTranslationEntity $entity)
 * @method void                         set(string $key, OptionTranslationEntity $entity)
 * @method OptionTranslationEntity[]    getIterator()
 * @method OptionTranslationEntity[]    getElements()
 * @method OptionTranslationEntity|null get(string $key)
 * @method OptionTranslationEntity|null first()
 * @method OptionTranslationEntity|null last()
 */
class OptionTranslationCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return OptionTranslationEntity::class;
    }
}
