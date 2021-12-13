<?php

namespace PostNl\Shipments\Facade;

use Firstred\PostNL\Exception\ResponseException;
use PostNl\Shipments\Factory\ApiFactory;

class CredentialsFacade
{
    /** @var ApiFactory  */
    private $apiFactory;

    public function __construct(ApiFactory $apiFactory)
    {
        $this->apiFactory = $apiFactory;
    }

    public function test(string $apiKey)
    {
        try {
            $apiClient = $this->apiFactory->createClient(
                $apiKey,
                [
                    'CustomerCode' => 'DEVC',
                    'CustomerNumber' => '11223344'
                ],
                [],
                true
            );

            dd($apiClient->generateBarcodeByCountryCode('NL'));
        } catch(ResponseException $e) {
            dd(json_decode($e->getResponse()->getBody()));
        }
    }
}
