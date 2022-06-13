<?php

namespace PostNL\Shopware6\Component\PostNL\Service;

use Firstred\PostNL\Entity\Request\GenerateLabel;
use Firstred\PostNL\Service\LabellingService as ApiLabellingService;

class LabellingService extends ApiLabellingService
{
    private static $insuranceProductCodes =  [3534, 3544, 3087, 3094];

    /**
     * @inheritDoc
     */
    public function buildGenerateLabelRequestREST(GenerateLabel $generateLabel, $confirm = true)
    {
        $apiKey = $this->postnl->getRestApiKey();
        $this->setService($generateLabel);
        $endpoint = $this->postnl->getSandbox() ? static::SANDBOX_ENDPOINT : static::LIVE_ENDPOINT;
        foreach ($generateLabel->getShipments() as $shipment) {
            if (in_array($shipment->getProductCodeDelivery(), static::$insuranceProductCodes)) {
                // Insurance behaves a bit strange w/ v2.2, falling back on v2.1
                $endpoint = str_replace('v2_2', 'v2_1', $endpoint);
            }
        }

        return $this->postnl->getRequestFactory()->createRequest(
            'POST',
            $endpoint.'?'.http_build_query([
                'confirm' => $confirm ? 'true' : 'false',
            ], '', '&', PHP_QUERY_RFC3986))
            ->withHeader('apikey', $apiKey)
            ->withHeader('Accept', 'application/json')
            ->withHeader('Content-Type', 'application/json;charset=UTF-8')
            ->withBody($this->postnl->getStreamFactory()->createStream(json_encode($generateLabel)));
    }
}
