<?php declare(strict_types=1);

namespace PostNL\Shopware6\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class EntityCustomFieldsException extends ShopwareHttpException
{
    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Entity \"{{entity}}\" does not contain custom fields";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'POSTNL__ATTRIBUTE_ENTITY_CUSTOM_FIELDS';
    }
}
