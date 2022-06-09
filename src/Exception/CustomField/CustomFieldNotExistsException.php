<?php

namespace PostNL\Shopware6\Exception\CustomField;

class CustomFieldNotExistsException extends CustomFieldException
{
    public function __construct(array $parameters, ?\Throwable $previous = null)
    {
        $message = "Custom field does not exist";
        parent::__construct($message, $parameters, $previous);
    }

    public function getErrorCode(): string
    {
        return 'POSTNL__CUSTOM_FIELD_NOT_EXISTS';
    }
}
