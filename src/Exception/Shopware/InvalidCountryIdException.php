<?php declare(strict_types=1);

namespace PostNL\Shipments\Exception\Shopware;

use Shopware\Core\Framework\ShopwareHttpException;

class InvalidCountryIdException extends ShopwareHttpException
{
    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Invalid country ID. Could not find a country entity for ID \"{{countryId}}\"";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'POSTNL__SHOPWARE_INVALID_COUNTRY_ID';
    }
}
