<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service\ResponseProcessor;

use Firstred\PostNL\Exception\CifDownException;
use Firstred\PostNL\Exception\CifException;
use Firstred\PostNL\Exception\HttpClientException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use PostNL\Shopware6\Component\PostNL\Entity\Response\ActivateReturnResponse;
use Psr\Http\Message\ResponseInterface;

/**
 * @since 2.0.0
 *
 * @internal
 */
interface ActivateReturnServiceResponseProcessorInterface
{

    /**
     * @param ResponseInterface $response
     *
     * @return ActivateReturnResponse
     *
     * @throws CifDownException
     * @throws CifException
     * @throws HttpClientException
     * @throws ResponseException
     * @throws InvalidConfigurationException
     * @since 2.0.0
     */
    public function processActivateReturnResponse(ResponseInterface $response): ActivateReturnResponse;
}