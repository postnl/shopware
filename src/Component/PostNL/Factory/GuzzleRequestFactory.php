<?php

namespace PostNL\Shopware6\Component\PostNL\Factory;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class GuzzleRequestFactory implements RequestFactoryInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $headers = [];

    /**
     * Creates a new PSR-7 request.
     *
     * @param string              $method
     * @param string|UriInterface $uri
     *
     * @return RequestInterface
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        return new Request($method, $uri, $this->headers);
    }

    /**
     * @param string $header
     * @param mixed $value
     * @return $this
     */
    public function addHeader(string $header, mixed $value): self
    {
        $this->headers[$header] = $value;
        return $this;
    }
}
