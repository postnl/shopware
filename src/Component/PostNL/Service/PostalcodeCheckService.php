<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service;

use Firstred\PostNL\Entity\Request\GenerateBarcode;
use Firstred\PostNL\Exception\CifDownException;
use Firstred\PostNL\Exception\CifException;
use Firstred\PostNL\Exception\HttpClientException;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use Firstred\PostNL\HttpClient\HttpClientInterface;
use Firstred\PostNL\Service\AbstractService;
use Firstred\PostNL\Service\RequestBuilder\BarcodeServiceRequestBuilderInterface;
use Firstred\PostNL\Service\RequestBuilder\Rest\BarcodeServiceRestRequestBuilder;
use Firstred\PostNL\Service\ResponseProcessor\BarcodeServiceResponseProcessorInterface;
use Firstred\PostNL\Service\ResponseProcessor\ResponseProcessorSettersTrait;
use Firstred\PostNL\Service\ResponseProcessor\Rest\BarcodeServiceRestResponseProcessor;
use ParagonIE\HiddenString\HiddenString;
use PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\PostalcodeCheckServiceRequestBuilderInterface;
use PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\Rest\PostalcodeCheckServiceRestRequestBuilder;
use PostNL\Shopware6\Component\PostNL\Service\ResponseProcessor\PostalcodeCheckServiceResponseProcessorInterface;
use PostNL\Shopware6\Component\PostNL\Service\ResponseProcessor\Rest\PostalcodeCheckServiceRestResponseProcessor;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @since 2.0.0
 *
 * @internal
 */
class PostalcodeCheckService extends AbstractService implements PostalCodeCheckServiceInterface
{
    use ResponseProcessorSettersTrait;

    protected PostalcodeCheckServiceRequestBuilderInterface $requestBuilder;
    protected PostalcodeCheckServiceResponseProcessorInterface $responseProcessor;

    /**
     * @param HiddenString                            $apiKey
     * @param bool                                    $sandbox
     * @param HttpClientInterface                     $httpClient
     * @param RequestFactoryInterface                 $requestFactory
     * @param StreamFactoryInterface                  $streamFactory
     */
    public function __construct(
        HiddenString $apiKey,
        bool $sandbox,
        HttpClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
    ) {
        parent::__construct(
            apiKey: $apiKey,
            sandbox: $sandbox,
            httpClient: $httpClient,
            requestFactory: $requestFactory,
            streamFactory: $streamFactory,
        );

        $this->requestBuilder = new PostalcodeCheckServiceRestRequestBuilder(
            apiKey: $this->apiKey,
            sandbox: $this->isSandbox(),
            requestFactory: $this->getRequestFactory(),
            streamFactory: $this->getStreamFactory(),
        );
        $this->responseProcessor = new PostalcodeCheckServiceRestResponseProcessor();
    }


    /**
     * @param HiddenString $apiKey
     *
     * @return static
     *
     * @since 2.0.0
     */
    public function setApiKey(HiddenString $apiKey): static
    {
        $this->requestBuilder->setApiKey(apiKey: $apiKey);

        return parent::setApiKey(apiKey: $apiKey);
    }

    /**
     * @param bool $sandbox
     *
     * @return static
     *
     * @since 2.0.0
     */
    public function setSandbox(bool $sandbox): static
    {
        $this->requestBuilder->setSandbox(sandbox: $sandbox);

        return parent::setSandbox(sandbox: $sandbox);
    }

    /**
     * @param RequestFactoryInterface $requestFactory
     *
     * @return static
     *
     * @since 2.0.0
     */
    public function setRequestFactory(RequestFactoryInterface $requestFactory): static
    {
        $this->requestBuilder->setRequestFactory(requestFactory: $requestFactory);

        return parent::setRequestFactory(requestFactory: $requestFactory);
    }

    /**
     * @param StreamFactoryInterface $streamFactory
     *
     * @return static
     *
     * @since 2.0.0
     */
    public function setStreamFactory(StreamFactoryInterface $streamFactory): static
    {
        $this->requestBuilder->setStreamFactory(streamFactory: $streamFactory);

        return parent::setStreamFactory(streamFactory: $streamFactory);
    }
}
