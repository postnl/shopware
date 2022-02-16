<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\Product\Aggregate\ProductOption;

use Firstred\PostNL\Entity\ProductOption;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                     add(ProductOptionEntity $entity)
 * @method void                     set(string $key, ProductOptionEntity $entity)
 * @method ProductOptionEntity[]    getIterator()
 * @method ProductOptionEntity[]    getElements()
 * @method ProductOptionEntity|null get(string $key)
 * @method ProductOptionEntity|null first()
 * @method ProductOptionEntity|null last()
 */
class ProductOptionCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductOptionEntity::class;
    }

    /**
     * @return array<ProductOption>
     */
    public function getApiEntities(): array {
        $arr = [];

        foreach($this->getElements() as $productOption) {
            $arr[] = $productOption->getApiEntity();
        }

        return $arr;
    }
}
