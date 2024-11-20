<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service\RequestBuilder;

use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use PostNL\Shopware6\Component\PostNL\Entity\Request\ActivateReturn;
use Psr\Http\Message\RequestInterface;

/**
 * @since 2.0.0
 *
 * @internal
 */
interface ActivateReturnServiceRequestBuilderInterface
{
    /**
     * Build the 'generate barcode' HTTP request.
     *
     * @param ActivateReturn $activateReturn
     *
     * @return RequestInterface
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     *
     * @since 2.0.0
     */
    public function buildActivateReturnRequest(ActivateReturn $activateReturn): RequestInterface;
}
