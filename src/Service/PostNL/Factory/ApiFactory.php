<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\PostNL\Factory;

use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Customer;
use Firstred\PostNL\PostNL;
use PostNL\Shipments\Component\PostNL\Factory\GuzzleRequestFactory;

class ApiFactory
{
    public function __construct() {
        //TODO ConfigService
    }

    public function createClient(string $apiKey, $sandbox = false, array $customerData = [], array $senderAddress = []): PostNL
    {
        $customer = Customer::create($customerData);
        $customer->setAddress((Address::create($senderAddress))->setAddressType('02'));

        $requestFactory = new GuzzleRequestFactory();
//        $requestFactory->addHeader();

        $client = new PostNL($customer, $apiKey, $sandbox);
        $client->setRequestFactory($requestFactory);

        return $client;
    }

    // TODO: createForSalesChannel($salesChannelId, $context);
}
