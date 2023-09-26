<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL;

use Firstred\PostNL\Exception\CifDownException;
use Firstred\PostNL\Exception\CifException;
use Firstred\PostNL\Exception\HttpClientException;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use Firstred\PostNL\HttpClient\HttpClientInterface;
use Firstred\PostNL\PostNL as BaseClient;
use Firstred\PostNL\Service\DeliveryDateServiceInterface;
use ParagonIE\HiddenString\HiddenString;
use PostNL\Shopware6\Component\PostNL\Entity\Request\PostalCode;
use PostNL\Shopware6\Component\PostNL\Entity\Response\PostalCodeResponse;
use PostNL\Shopware6\Component\PostNL\Service\DeliveryDateServiceDecorator;
use PostNL\Shopware6\Component\PostNL\Service\PostalcodeCheckService;
use PostNL\Shopware6\Component\PostNL\Service\PostalcodeCheckServiceInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class PostNL extends BaseClient
{
    /** @var PostalcodeCheckServiceInterface */
    protected PostalcodeCheckServiceInterface $postalcodeCheckService;

    public function getDeliveryDateService(): DeliveryDateServiceInterface
    {
        if (!isset($this->deliveryDateService)) {
            $this->setDeliveryDateService(service: new DeliveryDateServiceDecorator(
                apiKey: $this->apiKey,
                sandbox: $this->getSandbox(),
                httpClient: $this->getHttpClient(),
                requestFactory: $this->getRequestFactory(),
                streamFactory: $this->getStreamFactory(),
            ));
        }

        return $this->deliveryDateService;
    }


    /**
     * @return PostalcodeCheckServiceInterface
     * @throws \Firstred\PostNL\Exception\InvalidArgumentException
     */
    public function getPostalcodeCheckService(): PostalcodeCheckServiceInterface
    {
        if (!isset($this->postalcodeCheckService)) {
            $this->setPostalCodeCheckService(postalcodeCheckService: new PostalcodeCheckService(
                apiKey: $this->apiKey,
                sandbox: $this->getSandbox(),
                httpClient: $this->getHttpClient(),
                requestFactory: $this->getRequestFactory(),
                streamFactory: $this->getStreamFactory(),
            ));
        }
        return $this->postalcodeCheckService;
    }

    /**
     * @param PostalcodeCheckServiceInterface $postalcodeCheckService
     * @return void
     */
    public function setPostalcodeCheckService(PostalcodeCheckServiceInterface $postalcodeCheckService): void
    {
        $this->postalcodeCheckService = $postalcodeCheckService;
    }

    /**
     * @param string      $postalCode
     * @param int         $houseNumber
     * @param string|null $houseNumberAddition
     * @return PostalCodeResponse
     * @throws CifDownException
     * @throws CifException
     * @throws HttpClientException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws ResponseException
     */
    public function getPostalCode(string $postalCode, int $houseNumber, string $houseNumberAddition = null): PostalCodeResponse
    {
        return $this->getPostalcodeCheckService()->postalcodeCheck(postalCode: new PostalCode(postalCode: $postalCode, houseNumber: $houseNumber, houseNumberAddition: $houseNumberAddition));
    }

    public function setApiKey(HiddenString|string $apiKey): static
    {
        parent::setApiKey($apiKey);
        $this->getPostalcodeCheckService()->setApiKey($this->apiKey);
        return $this;
    }

    public function setSandbox(bool $sandbox): static
    {
        parent::setSandbox($sandbox);
        $this->getPostalcodeCheckService()->setSandbox($sandbox);
        return $this;
    }

    public function setHttpClient(HttpClientInterface $httpClient): static
    {
        parent::setHttpClient($httpClient);
        $this->getPostalcodeCheckService()->setHttpClient($this->httpClient);
        return $this;
    }

    public function setRequestFactory(RequestFactoryInterface $requestFactory): static
    {
        parent::setRequestFactory($requestFactory);
        $this->getPostalcodeCheckService()->setRequestFactory($requestFactory);
        return $this;
    }

    public function setStreamFactory(StreamFactoryInterface $streamFactory): static
    {
        parent::setStreamFactory($streamFactory);
        $this->getPostalcodeCheckService()->setStreamFactory($streamFactory);
        return $this;
    }
}
