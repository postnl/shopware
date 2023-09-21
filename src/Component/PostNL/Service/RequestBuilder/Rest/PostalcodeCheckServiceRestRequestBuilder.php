<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\Rest;

use Firstred\PostNL\Entity\AbstractEntity;
use Firstred\PostNL\Entity\Request\GenerateBarcode;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Service\RequestBuilder\Rest\AbstractRestRequestBuilder;
use PostNL\Shopware6\Component\PostNL\Entity\Request\PostalCode;
use PostNL\Shopware6\Component\PostNL\Service\PostalcodeCheckServiceInterface;
use PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\PostalcodeCheckServiceRequestBuilderInterface;
use Psr\Http\Message\RequestInterface;
use const PHP_QUERY_RFC3986;

/**
 * @since 2.0.0
 *
 * @internal
 */
class PostalcodeCheckServiceRestRequestBuilder extends AbstractRestRequestBuilder implements PostalcodeCheckServiceRequestBuilderInterface
{
    // Endpoints
    private const SANDBOX_ENDPOINT = 'https://api-sandbox.postnl.nl/shipment/checkout/v1/postalcodecheck';
    private const LIVE_ENDPOINT = 'https://api.postnl.nl/shipment/checkout/v1/postalcodecheck';

    /**
     * Build the 'postalcode check' HTTP request.
     *
     * @param PostalCode $postalCode
     *
     * @return RequestInterface
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     *
     * @since 2.0.0
     */
    public function buildPostalcodeCheckRequest(PostalCode $postalCode): RequestInterface
    {
        $this->setService(entity: $postalCode);

        return $this->getRequestFactory()->createRequest(
            method: 'GET',
            uri: ($this->isSandbox() ? static::SANDBOX_ENDPOINT : static::LIVE_ENDPOINT) . '?' . http_build_query(data: [
                'postalcode' => $postalCode->getPostalCode(),
                'housenumber' => $postalCode->getHouseNumber(),
                'housenumberaddition' => $postalCode->getHouseNumberAddition(),
            ], arg_separator: '&', encoding_type: PHP_QUERY_RFC3986)
        )
            ->withHeader('Accept', value: 'application/json')
            ->withHeader('apikey', value: $this->apiKey->getString());
    }

    /**
     * Set this service on the given entity.
     *
     * This lets the entity know for which service it should serialize.
     *
     * @param AbstractEntity $entity
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     *
     * @since 2.0.0
     */
    public function setService(AbstractEntity $entity): void
    {
        $entity->setCurrentService(currentService: PostalcodeCheckServiceInterface::class);

        parent::setService(entity: $entity);
    }
}
