<?php

namespace PostNL\Shopware6\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class MissingEntityAttributeStructException extends ShopwareHttpException
{
    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Missing attribute struct for entity \"{{entity}}\". Did you register the struct as a service with the tag \"postnl.attribute.struct.entity\"?";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'POSTNL__ATTRIBUTE_MISSING_ENTITY_ATTRIBUTE_STRUCT';
    }
}
