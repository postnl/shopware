<?php

namespace PostNL\Shipments\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class MissingTypeHandlerException extends ShopwareHttpException
{
    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Missing type handler for class \"{{type}}\"";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return "POSTNL__ATTRIBUTE_MISSING_TYPE_HANDLER";
    }
}
