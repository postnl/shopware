<?php

namespace PostNL\Shopware6\Service\PostNL\ApiExtension\Service;

use Firstred\PostNL\Exception\CifDownException;
use Firstred\PostNL\Exception\CifException;
use Firstred\PostNL\Exception\HttpClientException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\NotFoundException;
use Firstred\PostNL\Exception\ResponseException;
use Firstred\PostNL\Service\AbstractService;
use GuzzleHttp\Psr7\Message as PsrMessage;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Request\PostalCode;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Response\PostalCodeResponse;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Response\PostalCodeResult;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Exception\InvalidAddressException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PostalCodeCheckService extends AbstractService implements PostalCodeCheckServiceInterface
{
    // API Version
    const VERSION = '1';

    // Endpoints
    const LIVE_ENDPOINT = 'https://api.postnl.nl/shipment/checkout/v1/postalcodecheck';
    const SANDBOX_ENDPOINT = 'https://api-sandbox.postnl.nl/shipment/checkout/v1/postalcodecheck';

    const DOMAIN_NAMESPACE = 'https://postnl.nl/';


    /**
     * @throws CifDownException
     * @throws NotFoundException
     * @throws CifException
     * @throws HttpClientException
     * @throws InvalidArgumentException
     * @throws ResponseException
     * @throws InvalidConfigurationException
     * @throws InvalidAddressException
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

        if (isset($object->errors)) {
            throw new InvalidAddressException($object->errors[0]->detail);
        }


        if (200 === $response->getStatusCode()) {
            throw new ResponseException('Invalid API response', null, null, $response);
        }

        throw new NotFoundException('Unable to create postal code');
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

    /**
     * @param $response
     * @return mixed|PostalCodeResponse
     * @throws HttpClientException
     * @throws ResponseException
     */
    public function processSendPostalCodeCheckResponseREST($response)
    {
        $body = json_decode(static::getResponseText($response));

        $object = null;
        if (isset($body->errors)) {
            return $body;
        }

        if (is_array($body) && !empty($body)) {
            $postalResponses = [];
            foreach ($body as $postalCodeGroup) {
                $postalCodeResult = new PostalCodeResult($postalCodeGroup->city,
                    $postalCodeGroup->postalCode,
                    $postalCodeGroup->streetName,
                    $postalCodeGroup->houseNumber,
                    $postalCodeGroup->formattedAddress);
                $postalResponses[] = $postalCodeResult;
            }
            $object = new PostalCodeResponse($postalResponses);
        }
//        $this->setService($object);
        return $object;
    }

}
