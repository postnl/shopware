<?php

namespace PostNL\Shopware6\Component\PostNL\Service;

use Firstred\PostNL\Entity\Request\SendShipment;
use Firstred\PostNL\Service\ShippingService as ApiShippingService;

class ShippingService extends ApiShippingService
{
    /**
     * @inheritDoc
     */
    public function buildSendShipmentRequestREST(SendShipment $sendShipment, $confirm = true)
    {
        $apiKey = $this->postnl->getRestApiKey();
        $this->setService($sendShipment);

        return $this->postnl->getRequestFactory()->createRequest(
            'POST',
            ($this->postnl->getSandbox() ? static::SANDBOX_ENDPOINT : static::LIVE_ENDPOINT).'?'.http_build_query([
                'confirm' => $confirm ? 'true' : 'false',
            ], '', '&', PHP_QUERY_RFC3986)
        )
            ->withHeader('apikey', $apiKey)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json;charset=UTF-8')
            ->withBody($this->postnl->getStreamFactory()->createStream(json_encode($sendShipment)));
    }
}
