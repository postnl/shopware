<?php

declare(strict_types=1);

namespace PostNL\Shipments\Entity\ProductCode;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                         add(ProductCodeConfigEntity $entity)
 * @method void                         set(string $key, ProductCodeConfigEntity $entity)
 * @method ProductCodeConfigEntity[]    getIterator()
 * @method ProductCodeConfigEntity[]    getElements()
 * @method ProductCodeConfigEntity|null get(string $key)
 * @method ProductCodeConfigEntity|null first()
 * @method ProductCodeConfigEntity|null last()
 */
class ProductCodeConfigCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductCodeConfigEntity::class;
    }

    public function reduceToProperty(string $property): array
    {
        $map = $this->map(function(ProductCodeConfigEntity $element) use ($property) {
            return $element->get($property);
        });

        return array_unique(array_values($map));
    }
}
