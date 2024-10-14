<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service;

use Firstred\PostNL\HttpClient\HttpClientInterface;
use Firstred\PostNL\Service\AbstractService;
use Firstred\PostNL\Service\ResponseProcessor\ResponseProcessorSettersTrait;
use ParagonIE\HiddenString\HiddenString;
use PostNL\Shopware6\Component\PostNL\Entity\Request\ActivateReturn;
use PostNL\Shopware6\Component\PostNL\Entity\Request\PostalCode;
use PostNL\Shopware6\Component\PostNL\Entity\Response\PostalCodeResponse;
use PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\PostalcodeCheckServiceRequestBuilderInterface;
use PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\Rest\ActivateReturnServiceRestRequestBuilder;
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
class ActivateReturnService extends AbstractService implements ActivateReturnServiceInterface
{
    use ResponseProcessorSettersTrait;

    protected ActivateReturnServiceRestRequestBuilder $requestBuilder;
//    protected ?PostalcodeCheckServiceResponseProcessorInterface $responseProcessor;

    /**
     * @param HiddenString            $apiKey
     * @param bool                    $sandbox
     * @param HttpClientInterface     $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface  $streamFactory
     */
    public function __construct(
        HiddenString            $apiKey,
        bool                    $sandbox,
        HttpClientInterface     $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface  $streamFactory,
    )
    {
        parent::__construct(
            apiKey: $apiKey,
            sandbox: $sandbox,
            httpClient: $httpClient,
            requestFactory: $requestFactory,
            streamFactory: $streamFactory,
        );

        $this->requestBuilder = new ActivateReturnServiceRestRequestBuilder(
            apiKey: $this->apiKey,
            sandbox: $this->isSandbox(),
            requestFactory: $this->getRequestFactory(),
            streamFactory: $this->getStreamFactory(),
        );
//        $this->responseProcessor = new PostalcodeCheckServiceRestResponseProcessor();
    }

    public function activateReturn(ActivateReturn $activateReturn): void
    {
        $this
            ->getHttpClient()
            ->doRequest(request: $this->requestBuilder->buildActivateReturnRequest(activateReturn: $activateReturn));

//        return $this->responseProcessor->processPostalcodeCheckResponse(response: $response);
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
