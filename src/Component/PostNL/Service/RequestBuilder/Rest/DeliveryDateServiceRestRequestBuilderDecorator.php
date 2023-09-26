<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service\RequestBuilder\Rest;

use Firstred\PostNL\Entity\Request\GetSentDateRequest;
use Firstred\PostNL\Service\RequestBuilder\Rest\DeliveryDateServiceRestRequestBuilder;
use Psr\Http\Message\RequestInterface;

class DeliveryDateServiceRestRequestBuilderDecorator extends DeliveryDateServiceRestRequestBuilder
{
    const LIVE_ENDPOINT = 'https://api.postnl.nl/shipment/v2_2/calculate/date';
    const SANDBOX_ENDPOINT = 'https://api-sandbox.postnl.nl/shipment/v2_2/calculate/date';

    public function buildGetSentDateRequest(GetSentDateRequest $getSentDate): RequestInterface
    {
        $this->setService(entity: $getSentDate);

        $sentDate = $getSentDate->getGetSentDate();
        $query = [
            'DeliveryDate' => $sentDate->getDeliveryDate()->format('d-m-Y'), // Was changed with regard to SDk 2.0.2
        ];
        $query['CountryCode'] = $sentDate->getCountryCode();
        if ($duration = $sentDate->getShippingDuration()) {
            $query['ShippingDuration'] = $duration;
        }
        if ($postcode = $sentDate->getPostalCode()) {
            $query['PostalCode'] = $postcode;
        }
        if ($city = $sentDate->getCity()) {
            $query['City'] = $city;
        }
        if ($houseNr = $sentDate->getHouseNr()) {
            $query['HouseNr'] = $houseNr;
        }
        if ($houseNrExt = $sentDate->getHouseNrExt()) {
            $query['HouseNrExt'] = $houseNrExt;
        }

        $endpoint = '/shipping?' . http_build_query(data: $query, numeric_prefix: '', arg_separator: '&', encoding_type: PHP_QUERY_RFC3986);

        return $this->getRequestFactory()->createRequest(
            method: 'GET',
            uri: ($this->isSandbox() ? static::SANDBOX_ENDPOINT : self::LIVE_ENDPOINT) . $endpoint,
        )
            ->withHeader('apikey', value: $this->apiKey->getString())
            ->withHeader('Accept', value: 'application/json');
    }
}