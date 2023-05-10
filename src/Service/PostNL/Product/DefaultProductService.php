<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL\Product;

use PostNL\Shopware6\Defaults;

class DefaultProductService
{
    public function getFallback(
        string $sourceZone,
        string $destinationZone,
        string $deliveryType
    ) {
        $constants = Defaults::getConstants();

        $constant = strtoupper(sprintf(
            '%s_%s_%s_%s',
            'product',
            $deliveryType,
            $sourceZone,
            $destinationZone,
        ));

        if (array_key_exists($constant, $constants) && !empty($constants[$constant])) {
            return $constants[$constant];
        }

        throw new \Exception("No fallback product. Please check whether this combination should exist.");
    }
}
