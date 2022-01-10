<?php declare(strict_types=1);

namespace PostNL\Shipments\Factory;

use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Customer;
use Firstred\PostNL\PostNL;

class ApiFactory
{
    public function __construct() {
        //TODO ConfigService
    }

    public function createClient(string $apiKey, $sandbox = false, array $customerData = [], array $senderAddress = []): PostNL
    {
        $customer = Customer::create($customerData);
        $customer->setAddress((Address::create($senderAddress))->setAddressType('02'));

        return new PostNL($customer, $apiKey, $sandbox);
    }

    // TODO: createForSalesChannel($salesChannelId, $context);
}
