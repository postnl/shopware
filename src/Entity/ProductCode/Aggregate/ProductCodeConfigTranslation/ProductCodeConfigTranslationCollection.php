<?php

declare(strict_types=1);

namespace PostNl\Shipments\Entity\ProductCode\Aggregate\ProductCodeConfigTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                                    add(ProductCodeConfigTranslationEntity $entity)
 * @method void                                    set(string $key, ProductCodeConfigTranslationEntity $entity)
 * @method ProductCodeConfigTranslationEntity[]    getIterator()
 * @method ProductCodeConfigTranslationEntity[]    getElements()
 * @method ProductCodeConfigTranslationEntity|null get(string $key)
 * @method ProductCodeConfigTranslationEntity|null first()
 * @method ProductCodeConfigTranslationEntity|null last()
 */
class ProductCodeConfigTranslationCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return ProductCodeConfigTranslationEntity::class;
    }
}
