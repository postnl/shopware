<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\Rest;

use Firstred\PostNL\Entity\AbstractEntity;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Service\RequestBuilder\Rest\AbstractRestRequestBuilder;
use GuzzleHttp\Psr7\Utils;
use PostNL\Shopware6\Component\PostNL\Entity\Request\ActivateReturn;
use PostNL\Shopware6\Component\PostNL\Service\PostalcodeCheckServiceInterface;
use PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\ActivateReturnServiceRequestBuilderInterface;
use Psr\Http\Message\RequestInterface;

/**
 * @since 2.0.0
 *
 * @internal
 */
class ActivateReturnServiceRestRequestBuilder extends AbstractRestRequestBuilder implements ActivateReturnServiceRequestBuilderInterface
{
    // Endpoints
    private const SANDBOX_ENDPOINT = 'https://api-sandbox.postnl.nl/parcels/v1/shipment/activatereturn';
    private const LIVE_ENDPOINT    = 'https://api.postnl.nl/parcels/v1/shipment/activatereturn';

    /**
     * @param ActivateReturn $activateReturn
     *
     * @return RequestInterface
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     *
     * @since 2.0.0
     */
    public function buildActivateReturnRequest(ActivateReturn $activateReturn): RequestInterface
    {
        $this->setService(entity: $activateReturn);

        $body = [
            "CustomerNumber" => $activateReturn->getCustomerNumber(),
            "CustomerCode"   => $activateReturn->getCustomerCode(),
            "Barcode"        => $activateReturn->getBarcode(),
        ];

        return $this->getRequestFactory()->createRequest(
            method: 'POST',
            uri   : ($this->isSandbox() ? static::SANDBOX_ENDPOINT : static::LIVE_ENDPOINT)
        )
                    ->withHeader('Accept', value: 'application/json')
                    ->withHeader('Content-Type', value: 'application/json')
                    ->withHeader('apikey', value: $this->apiKey->getString())
                    ->withBody(Utils::streamFor(json_encode($body)));
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
