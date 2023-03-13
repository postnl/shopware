<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service;

use Firstred\PostNL\Entity\CutOffTime;
use Firstred\PostNL\Entity\Request\GetDeliveryDate;
use Firstred\PostNL\Entity\Request\GetSentDateRequest;
use Firstred\PostNL\Service\DeliveryDateService as ApiDeliveryDateService;
use Psr\Http\Message\RequestInterface;

class DeliveryDateService extends ApiDeliveryDateService
{

    /**
     * Build the GetDeliveryDate request for the REST API.
     *
     * @param GetDeliveryDate $getDeliveryDate
     *
     * @return RequestInterface
     *
     * @since 1.0.0
     */
    public function buildGetDeliveryDateRequestREST(GetDeliveryDate $getDeliveryDate)
    {
        $apiKey = $this->postnl->getRestApiKey();
        $this->setService($getDeliveryDate);
        $deliveryDate = $getDeliveryDate->getGetDeliveryDate();

        $query = [
            'AllowSundaySorting' => in_array($deliveryDate->getAllowSundaySorting(), [true, 'true', 1], true) ? 'true' : 'false',
            'ShippingDate' => $deliveryDate->getShippingDate()->format('d-m-Y H:i:s'),
            'Options'      => 'Daytime',
        ];
        if ($shippingDuration = $deliveryDate->getShippingDuration()) {
            $query['ShippingDuration'] = $shippingDuration;
        }

        $times = $deliveryDate->getCutOffTimes();
        if (!is_array($times)) {
            $times = [];
        }

        $key = array_search('00', array_map(function ($time) {
            /* @var CutOffTime $time */
            return $time->getDay();
        }, $times));
        if (false !== $key) {
            $query['CutOffTime'] = date('H:i:s', strtotime($times[$key]->getTime()));
        } else {
            $query['CutOffTime'] = '15:30:00';
        }

        // There need to be more cut off times besides the default 00 one in order to override
        if (count($times) > 1) {
            foreach (range(1, 7) as $day) {
                $dayName = date('l', strtotime("Sunday +{$day} days"));

                $key = array_search(str_pad((string) $day, 2, '0', STR_PAD_LEFT), array_map(function ($time) {
                    /* @var CutOffTime $time */
                    return $time->getDay();
                }, $times));

                if (false !== $key) {
                    $query["CutOffTime$dayName"] = date('H:i:s', strtotime($times[$key]->getTime()));
                    $query["Available$dayName"] = 'true';
                } else {
                    $query["CutOffTime$dayName"] = '00:00:00';
                    $query["Available$dayName"] = 'false';
                }
            }
        }

        if ($postcode = $deliveryDate->getPostalCode()) {
            $query['PostalCode'] = $postcode;
        }
        $query['CountryCode'] = $deliveryDate->getCountryCode();
        if ($originCountryCode = $deliveryDate->getOriginCountryCode()) {
            $query['OriginCountryCode'] = $originCountryCode;
        }
        if ($city = $deliveryDate->getCity()) {
            $query['City'] = $city;
        }
        if ($houseNr = $deliveryDate->getHouseNr()) {
            $query['HouseNr'] = $houseNr;
        }
        if ($houseNrExt = $deliveryDate->getHouseNrExt()) {
            $query['HouseNrExt'] = $houseNrExt;
        }
        if (is_array($deliveryDate->getOptions())) {
            foreach ($deliveryDate->getOptions() as $option) {
                if (strcasecmp('Daytime', $option) === 0) {
                    continue;
                }

                $query['Options'] .= ",$option";
            }
        }

        $endpoint = '/delivery?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        return $this->postnl->getRequestFactory()->createRequest(
            'GET',
            ($this->postnl->getSandbox() ? static::SANDBOX_ENDPOINT : static::LIVE_ENDPOINT).$endpoint
        )
            ->withHeader('apikey', $apiKey)
            ->withHeader('Accept', 'application/json');
    }


    public function buildGetSentDateRequestREST(GetSentDateRequest $getSentDate)
    {

        $apiKey = $this->postnl->getRestApiKey();
        $this->setService($getSentDate);

        $sentDate = $getSentDate->getGetSentDate();


        $query = [
            'DeliveryDate' => date_format($sentDate->getDeliveryDate(), "d-m-Y"),
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


        $endpoint = '/shipping?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);


        return $this->postnl->getRequestFactory()->createRequest(
            'GET',
            ($this->postnl->getSandbox() ? static::SANDBOX_ENDPOINT : static::LIVE_ENDPOINT) . $endpoint
        )
            ->withHeader('apikey', $apiKey)
            ->withHeader('Accept', 'application/json');
    }
}
