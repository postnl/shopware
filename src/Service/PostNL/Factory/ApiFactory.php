<?php declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL\Factory;

use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Customer;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shopware6\Component\PostNL\Factory\GuzzleRequestFactory;
use PostNL\Shopware6\Component\PostNL\PostNL;
use PostNL\Shopware6\Exception\PostNL\ClientCreationException;
use PostNL\Shopware6\Service\PostNL\VersionProvider;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;

class ApiFactory
{
    protected ConfigService $configService;
    protected VersionProvider $versionProvider;
    protected LoggerInterface $logger;

    public function __construct(
        ConfigService $configService,
        VersionProvider $versionProvider,
        LoggerInterface $logger
    )
    {
        $this->configService = $configService;
        $this->versionProvider = $versionProvider;
        $this->logger = $logger;
    }

    public function createClient(
        string $apiKey,
        bool   $sandbox = false,
        array  $customerData = [],
        array  $senderAddress = []
    ): PostNL
    {
        $this->logger->debug("Creating API client", [
            'apiKey' => $this->obfuscateApiKey($apiKey),
            'sandbox' => $sandbox,
            'customerData' => $customerData,
            'senderAddress' => $senderAddress,
        ]);

        try {
            $customer = Customer::create($customerData);
            $customer->setCollectionLocation('123456');
            $customer->setAddress((Address::create($senderAddress))->setAddressType('02'));

            $requestFactory = new GuzzleRequestFactory();
            $requestFactory->addHeader('SourceSystem', 25);
            $requestFactory->addHeader('X-PostNL-Client-Versions', $this->versionProvider->getAllAsString(Context::createDefaultContext()));

            if (function_exists("php_uname")) {
                $requestFactory->addHeader('X-PostNL-Client-Info', php_uname());
            }

            $client = new PostNL($customer, $apiKey, $sandbox);
            $client->setRequestFactory($requestFactory);

            return $client;
        } catch (InvalidArgumentException $e) {
            $this->logger->critical($e->getMessage(), [
                'apiKey' => $this->obfuscateApiKey($apiKey),
                'sandbox' => $sandbox,
                'customerData' => $customerData,
                'senderAddress' => $senderAddress,
            ]);

            throw new ClientCreationException([
                'apiKey' => $this->obfuscateApiKey($apiKey),
                'sandbox' => $sandbox,
                'customerData' => $customerData,
                'senderAddress' => $senderAddress,
            ], $e);
        }
    }

    public function createClientForSalesChannel(string $salesChannelId, Context $context): PostNL
    {
        $this->logger->debug("Creating API client for saleschannel", [
            'salesChannelId' => $salesChannelId,
        ]);

        $config = $this->configService->getConfiguration($salesChannelId, $context);

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
