<?php

namespace PostNL\Shipments\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class MissingReturnTypeException extends ShopwareHttpException
{
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Missing return type declaration for method \"{{method}}\" in class \"{{class}}\"";
        parent::__construct($message, $parameters, $e);
    }

    public function getErrorCode(): string
    {
        return 'POSTNL__ATTRIBUTE_MISSING_RETURN_TYPE';
    }
}
