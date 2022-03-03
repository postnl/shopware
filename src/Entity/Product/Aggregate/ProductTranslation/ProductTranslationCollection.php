<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Product\Aggregate\ProductTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                          add(ProductTranslationEntity $entity)
 * @method void                          set(string $key, ProductTranslationEntity $entity)
 * @method ProductTranslationEntity[]    getIterator()
 * @method ProductTranslationEntity[]    getElements()
 * @method ProductTranslationEntity|null get(string $key)
 * @method ProductTranslationEntity|null first()
 * @method ProductTranslationEntity|null last()
 */
class ProductTranslationCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductTranslationEntity::class;
    }
}
