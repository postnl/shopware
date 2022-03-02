<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\Product;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(ProductEntity $entity)
 * @method void               set(string $key, ProductEntity $entity)
 * @method ProductEntity[]    getIterator()
 * @method ProductEntity[]    getElements()
 * @method ProductEntity|null get(string $key)
 * @method ProductEntity|null first()
 * @method ProductEntity|null last()
 */
class ProductCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductEntity::class;
    }

    /**
     * @param string $property
     * @return array<mixed>
     */
    public function reduceToProperty(string $property): array
    {
        $map = $this->map(function(ProductEntity $element) use ($property) {
            return $element->get($property);
        });

        return array_unique(array_values($map));
    }
}
