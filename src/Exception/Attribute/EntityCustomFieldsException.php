<?php

namespace PostNL\Shipments\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class EntityCustomFieldsException extends ShopwareHttpException
{
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Entity \"{{entity}}\" does not contain custom fields";
        parent::__construct($message, $parameters, $e);
    }

    public function getErrorCode(): string
    {
        return 'POSTNL__ATTRIBUTE_ENTITY_CUSTOM_FIELDS';
    }
}
