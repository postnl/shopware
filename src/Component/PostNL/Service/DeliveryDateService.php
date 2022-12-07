<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service;

use Firstred\PostNL\Entity\Request\GetSentDateRequest;
use Firstred\PostNL\Service\DeliveryDateService as ApiDeliveryDateService;

class DeliveryDateService extends ApiDeliveryDateService
{
 public function buildGetSentDateRequestREST(GetSentDateRequest $getSentDate)
 {

     $apiKey = $this->postnl->getRestApiKey();
     $this->setService($getSentDate);

     $sentDate = $getSentDate->getGetSentDate();


     $query = [
         'DeliveryDate' => date_format($sentDate->getDeliveryDate(),"d-m-Y")
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


     $endpoint = '/shipping?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);


     return $this->postnl->getRequestFactory()->createRequest(
         'GET',
         ($this->postnl->getSandbox() ? static::SANDBOX_ENDPOINT : static::LIVE_ENDPOINT).$endpoint
     )
         ->withHeader('apikey', $apiKey)
         ->withHeader('Accept', 'application/json');
 }
}
