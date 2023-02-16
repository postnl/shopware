<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Exception\PostNL;

use Shopware\Core\Framework\ShopwareHttpException;

class InvalidSourceCountryException extends ShopwareHttpException
{

    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Invalid source zone. Shipping is not supported for country \"{{sourceCountryIso}}\"";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'POSTNL__INVALID_SOURCE_COUNTRY';
    }
}
