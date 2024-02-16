<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Defaults;

class CustomFieldHelper
{
    public static function merge(array &$object, array ...$data): void
    {
        if(!array_key_exists('customFields', $object) || is_null($object['customFields'])) {
            $object['customFields'] = [];
        }

        $object['customFields'][Defaults::CUSTOM_FIELDS_KEY] = array_merge(
               $object['customFields'][Defaults::CUSTOM_FIELDS_KEY] ?? [],
            ...$data
        );
    }
}