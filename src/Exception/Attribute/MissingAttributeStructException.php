<?php

namespace PostNL\Shipments\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class MissingAttributeStructException extends ShopwareHttpException
{
    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Missing attribute struct. Attempted to create \"{{class}}\" but the class was not found.";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'POSTNL__ATTRIBUTE_MISSING_STRUCT';
    }
}
