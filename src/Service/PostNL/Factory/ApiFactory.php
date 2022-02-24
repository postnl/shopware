<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\PostNL\Factory;

use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Customer;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\PostNL;
use PostNL\Shipments\Component\PostNL\Factory\GuzzleRequestFactory;
use PostNL\Shipments\Service\Shopware\ConfigService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;

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
        dd(func_get_args());
        $this->logger->debug("Creating API client", [
            'apiKey' => $this->obfuscateApiKey($apiKey),
            'sandbox' => $sandbox,
            'customerData' => $customerData,
            'senderAddress' => $senderAddress,
        ]);

        try {
            $customer = Customer::create($customerData);
            $customer->setAddress((Address::create($senderAddress))->setAddressType('02'));

            $requestFactory = new GuzzleRequestFactory();
//            $requestFactory->addHeader();

            $client = new PostNL($customer, $apiKey, $sandbox);
            $client->setRequestFactory($requestFactory);

            return $client;
        } catch(InvalidArgumentException $e) {
            $this->logger->critical($e->getMessage(), [
                'apiKey' => $this->obfuscateApiKey($apiKey),
                'sandbox' => $sandbox,
                'customerData' => $customerData,
                'senderAddress' => $senderAddress,
            ]);

            throw new \ClientCreationException([
                'apiKey' => $this->obfuscateApiKey($apiKey),
                'sandbox' => $sandbox,
                'customerData' => $customerData,
                'senderAddress' => $senderAddress,
            ], $e);
        }
    }

    /**
     * @param string $salesChannelId
     * @param Context $context
     * @return PostNL
     */
    public function createClientForSalesChannel(string $salesChannelId, Context $context): PostNL
    {
        $this->logger->debug("Creating API client for saleschannel", [
            'salesChannelId' => $salesChannelId
        ]);

        $config = $this->configService->getConfiguration($salesChannelId, $context);
dump($config);

    print(
        json_encode($config)
//        $config->getSenderAddress()->getVarsForApi()
    );
dd();
        return $this->createClient(
            $config->isSandboxMode()
                ? $config->getSandboxApiKey()
                : $config->getProductionApiKey(),
            $config->isSandboxMode(),
            $config->getCustomerData()->getVarsForApi(),
            $config->getSenderAddress()->getVarsForApi()
        );
    }

    public function obfuscateApiKey(string $apiKey, $visibleCharacters = 6): string
    {
        return sprintf("%s..%s", substr($apiKey, 0, $visibleCharacters), substr($apiKey, -$visibleCharacters));
    }
}
