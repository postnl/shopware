<?php

namespace PostNL\Shopware6\Service\PostNL\ApiExtension\Service;

use Firstred\PostNL\Exception\NotFoundException;
use Firstred\PostNL\Exception\ResponseException;
use Firstred\PostNL\Service\AbstractService;
use GuzzleHttp\Psr7\Message as PsrMessage;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Request\PostalCode;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Response\PostalCodeResponse;
use Psr\Cache\CacheItemInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PostalCodeCheckService extends AbstractService implements PostalCodeCheckServiceInterface
{
    // API Version
    const VERSION = '1';

    // Endpoints
    const LIVE_ENDPOINT = 'https://api.postnl.nl/v1/shipment/checkout/v1/postalcodecheck';
    const SANDBOX_ENDPOINT = 'https://api-sandbox.postnl.nl/v1/shipment/checkout/v1/postalcodecheck';

    const DOMAIN_NAMESPACE = 'http://postnl.nl/';


    /**
     * @throws \Firstred\PostNL\Exception\CifDownException
     * @throws NotFoundException
     * @throws \Firstred\PostNL\Exception\CifException
     * @throws \Firstred\PostNL\Exception\HttpClientException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws ResponseException
     * @throws \Firstred\PostNL\Exception\InvalidConfigurationException
     */
    public function sendPostalCodeCheckRest(PostalCode $postalCode): PostalCodeResponse
    {
        $item = $this->retrieveCachedItem($postalCode->getId());
        $response = null;

        if ($item instanceof CacheItemInterface && $item->isHit()) {
            $response = $item->get();
            try {
                $response = PsrMessage::parseResponse($response);
            } catch (InvalidArgumentException $e) {
            }
        }
        if (!$response instanceof ResponseInterface) {
            $response = $this->postnl->getHttpClient()->doRequest(
                $this->buildSendPostalCodeCheckRequestREST($postalCode)
            );

            static::validateRESTResponse($response);
        }

        $object = $this->processSendPostalCodeCheckResponseREST($response);
        if ($object instanceof PostalCodeResponse) {
            if ($item instanceof CacheItemInterface
                && $response instanceof ResponseInterface
                && 200 === $response->getStatusCode()
            ) {
                $item->set(PsrMessage::toString($response));
                $this->cacheItem($item);
            }

            return $object;
        }

        if (200 === $response->getStatusCode()) {
            throw new ResponseException('Invalid API response', null, null, $response);
        }

        throw new NotFoundException('Unable to create shipment');
    }


    /**
     * @param PostalCode $postalCode
     * @return RequestInterface
     */
    public function buildSendPostalCodeCheckRequestREST(PostalCode $postalCode): RequestInterface
    {
        $apiKey = $this->postnl->getRestApiKey();
        $this->setService($postalCode);

        return $this->postnl->getRequestFactory()->createRequest(
            'POST',
            ($this->postnl->getSandbox() ? static::SANDBOX_ENDPOINT : static::LIVE_ENDPOINT)
        )
            ->withHeader('apikey', $apiKey)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json;charset=UTF-8')
            ->withBody($this->postnl->getStreamFactory()->createStream(json_encode($postalCode)));
    }

    public function processSendPostalCodeCheckResponseREST($response): ?PostalCodeResponse
    {
        $body = json_decode(static::getResponseText($response));
        if (isset($body->ResponseShipments)) {
            /** @var PostalCodeResponse $object */
            $object = PostalCodeResponse::JsonDeserialize((object) ['SendShipmentResponse' => $body]);
            $this->setService($object);

            return $object;
        }

        return null;
    }

}
