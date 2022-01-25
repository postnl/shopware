<?php

namespace PostNL\Shipments\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class MissingTypeHandlerException extends ShopwareHttpException
{
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Missing type handler for class \"{{type}}\"";
        parent::__construct($message, $parameters, $e);
    }

    public function getErrorCode(): string
    {
        return "POSTNL__ATTRIBUTE_MISSING_TYPE_HANDLER";
    }
}
