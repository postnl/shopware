<?php

namespace PostNL\Shipments\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class MissingPropertyAccessorMethodException extends ShopwareHttpException
{
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        if(is_array($parameters['example'])) {
            $parameters['example'] = implode(', ', $parameters['example']);
        }

        $message = "Missing property accessor method for property \"{{property}}\" in class \"{{class}}\". Possible methods: {{example}}.";
        parent::__construct($message, $parameters, $e);
    }

    public function getErrorCode(): string
    {
        return 'POSTNL__ATTRIBUTE_MISSING_PROPERTY_ACCESSOR';
    }
}
