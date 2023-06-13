<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Entity\Option;

use Firstred\PostNL\Entity\ProductOption;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add(OptionEntity $entity)
 * @method void              set(string $key, OptionEntity $entity)
 * @method OptionEntity[]    getIterator()
 * @method OptionEntity[]    getElements()
 * @method OptionEntity|null get(string $key)
 * @method OptionEntity|null first()
 * @method OptionEntity|null last()
 */
class OptionCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return OptionEntity::class;
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
