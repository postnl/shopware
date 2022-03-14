<?php declare(strict_types=1);

namespace PostNL\Shopware6\Exception\PostNL;

use Shopware\Core\Framework\ShopwareHttpException;

class ClientCreationException extends ShopwareHttpException
{
    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        $message = "Could not create a PostNL API Client";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'POSTNL__POSTNL_CLIENT_CREATION';
    }
}
