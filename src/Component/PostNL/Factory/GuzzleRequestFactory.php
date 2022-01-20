<?php

namespace PostNL\Shipments\Component\PostNL\Factory;

use Firstred\PostNL\Factory\RequestFactoryInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class GuzzleRequestFactory implements RequestFactoryInterface
{
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
        return (new Request($method, $uri, $this->headers));
    }

    /**
     * @param $header
     * @param $value
     * @return $this
     */
    public function addHeader($header, $value): self
    {
        $this->headers[$header] = $value;
        return $this;
    }
}
