<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\Product\Aggregate\ProductOptionTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                                add(ProductOptionTranslationEntity $entity)
 * @method void                                set(string $key, ProductOptionTranslationEntity $entity)
 * @method ProductOptionTranslationEntity[]    getIterator()
 * @method ProductOptionTranslationEntity[]    getElements()
 * @method ProductOptionTranslationEntity|null get(string $key)
 * @method ProductOptionTranslationEntity|null first()
 * @method ProductOptionTranslationEntity|null last()
 */
class ProductOptionTranslationCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductOptionTranslationEntity::class;
    }
}
