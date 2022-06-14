<?php

namespace PostNL\Shopware6\Component\PostNL\Factory;

use Firstred\PostNL\Factory\RequestFactoryInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class GuzzleRequestFactory implements RequestFactoryInterface
{
    /**
     * @var array<string, mixed>
     */
    private $headers = [];

    /**
     * Creates a new PSR-7 request.
     *
     * @param string              $method
     * @param string|UriInterface $uri
     *
     * @return RequestInterface
     */
    public function createRequest($method, $uri)
    {
        return new Request($method, $uri, $this->headers);
    }

    /**
     * @param string $header
     * @param mixed $value
     * @return $this
     */
    public function addHeader(string $header, $value): self
    {
        $this->headers[$header] = $value;
        return $this;
    }
}
