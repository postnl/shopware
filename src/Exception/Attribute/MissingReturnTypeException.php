<?php declare(strict_types=1);

namespace PostNL\Shopware6\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class MissingReturnTypeException extends ShopwareHttpException
{
    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Missing return type declaration for method \"{{method}}\" in class \"{{class}}\"";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'POSTNL__ATTRIBUTE_MISSING_RETURN_TYPE';
    }
}
