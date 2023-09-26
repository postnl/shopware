<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service;

use DateInterval;
use DateTimeInterface;
use Firstred\PostNL\HttpClient\HttpClientInterface;
use Firstred\PostNL\Service\DeliveryDateService;
use ParagonIE\HiddenString\HiddenString;
use PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\Rest\DeliveryDateServiceRestRequestBuilderDecorator;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class DeliveryDateServiceDecorator extends DeliveryDateService
{
    public function __construct(
        HiddenString                       $apiKey,
        bool                               $sandbox,
        HttpClientInterface                $httpClient,
        RequestFactoryInterface            $requestFactory,
        StreamFactoryInterface             $streamFactory,
        CacheItemPoolInterface             $cache = null,
        DateInterval|DateTimeInterface|int $ttl = null
    )
    {
        parent::__construct($apiKey, $sandbox, $httpClient, $requestFactory, $streamFactory, $cache, $ttl);

        $this->requestBuilder = new DeliveryDateServiceRestRequestBuilderDecorator(
            apiKey: $this->apiKey,
            sandbox: $this->isSandbox(),
            requestFactory: $this->getRequestFactory(),
            streamFactory: $this->getStreamFactory(),
        );
    }
}