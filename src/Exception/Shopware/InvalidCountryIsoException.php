<?php declare(strict_types=1);

namespace PostNL\Shopware6\Exception\Shopware;

use Shopware\Core\Framework\ShopwareHttpException;

class InvalidCountryIsoException extends ShopwareHttpException
{
    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Invalid country ISO code. Could not find a country entity for ISO code \"{{isoCode}}\"";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'POSTNL__SHOPWARE_INVALID_COUNTRY_ISO';
    }
}
