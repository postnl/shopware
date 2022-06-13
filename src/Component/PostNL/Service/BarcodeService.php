<?php

namespace PostNL\Shopware6\Component\PostNL\Service;

use Firstred\PostNL\Entity\Request\GenerateBarcode;
use Firstred\PostNL\Service\BarcodeService as ApiBarcodeService;

class BarcodeService extends ApiBarcodeService
{
    /**
     * @inheritDoc
     */
    public function buildGenerateBarcodeRequestREST(GenerateBarcode $generateBarcode)
    {
        $apiKey = $this->postnl->getRestApiKey();
        $this->setService($generateBarcode);

        return $this->postnl->getRequestFactory()->createRequest(
            'GET',
            ($this->postnl->getSandbox() ? static::SANDBOX_ENDPOINT : static::LIVE_ENDPOINT)
            .'?'.http_build_query([
                'CustomerCode'   => $generateBarcode->getCustomer()->getCustomerCode(),
                'CustomerNumber' => $generateBarcode->getCustomer()->getCustomerNumber(),
                'Type'           => $generateBarcode->getBarcode()->getType(),
                'Serie'          => $generateBarcode->getBarcode()->getSerie(),
                'Range'          => $generateBarcode->getBarcode()->getRange(),
            ], '', '&', PHP_QUERY_RFC3986)
        )
            ->withHeader('Accept', 'application/json')
            ->withHeader('apikey', $apiKey)
            ;
    }
}
