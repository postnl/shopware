<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Struct;

use PostNL\Shopware6\Component\PostNL\Entity\Response\PostalCodeResponse;
use Shopware\Core\Framework\Struct\Collection;

/**
 * @property array<array-key, PostalCodeStruct> $elements
 * @method __construct(PostalCodeStruct[] $elements)
 * @method void add(PostalCodeStruct $element)
 * @method void set($key, PostalCodeStruct $element)
 * @method PostalCodeStruct|null get($key)
 * @method PostalCodeStruct[] getElements()
 * @method PostalCodeStruct[] jsonSerialize()
 * @method PostalCodeStruct|null first()
 * @method PostalCodeStruct|null getAt(int $position)
 * @method PostalCodeStruct|null last()
 * @method \Generator<PostalCodeStruct> getIterator()
 * @implements \IteratorAggregate<array-key, PostalCodeStruct>
 */
class PostalCodeCollection extends Collection
{
    public static function createFromPostalCodeResponse(PostalCodeResponse $response)
    {
        $collection = new static();

        foreach ($response->getPostalCodeResult() as $result) {
            $collection->add(PostalCodeStruct::createFromPostalCodeResult($result));
        }

        return $collection;
    }

    protected function getExpectedClass(): ?string
    {
        return PostalCodeStruct::class;
    }
}