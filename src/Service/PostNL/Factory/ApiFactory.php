<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\PostNL\Factory;

use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Customer;
use Firstred\PostNL\PostNL;
use PostNL\Shipments\Service\Shopware\ConfigService;
use Psr\Log\LoggerInterface;

class ApiFactory
{
    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ConfigService $configService
     * @param LoggerInterface $logger
     */
    public function __construct(ConfigService $configService, LoggerInterface $logger)
    {
        $this->configService = $configService;
        $this->logger = $logger;
    }

    public function createClient(
        string $apiKey,
        bool $sandbox = false,
        array $customerData = [],
        array $senderAddress = []
    ): PostNL
    {
        $this->logger->debug("Creating API client", [
            'apiKey' => sprintf("%s..%s", substr($apiKey, 0, 6), substr($apiKey, -6)),
            'sandbox' => $sandbox,
            'customerData' => $customerData,
            'senderAddress' => $senderAddress,
        ]);

        $customer = Customer::create($customerData);
        $customer->setAddress((Address::create($senderAddress))->setAddressType('02'));

        return new PostNL($customer, $apiKey, $sandbox);
    }

    /**
     * @param string $salesChannelId
     * @return PostNL
     */
    public function createClientForSalesChannel(string $salesChannelId): PostNL
    {
        $this->logger->debug("Creating API client for saleschannel", [
            'salesChannelId' => $salesChannelId
        ]);

        $config = $this->configService->getConfiguration($salesChannelId);

        $client = $this->createClient(
            $config->isSandboxMode()
                ? $config->getSandboxApiKey()
                : $config->getProductionApiKey(),
            $config->isSandboxMode(),
            $config->getCustomerData()->getVarsForApi()
        );

        dd($config, $client);
    }

}
