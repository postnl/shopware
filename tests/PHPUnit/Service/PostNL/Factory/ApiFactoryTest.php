<?php
declare(strict_types=1);

namespace PostNL\tests\Service\PostNL\Factory;

use Firstred\PostNL\PostNL;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
use PostNL\Shopware6\Struct\Config\CustomerDataStruct;
use PostNL\Shopware6\Struct\Config\SenderAddressStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;

/**
 * @coversDefaultClass \PostNL\Shopware6\Service\PostNL\Factory\ApiFactory
 */
class ApiFactoryTest extends TestCase
{
    private ApiFactory $apiFactory;

    private function createApiFactory(
        ?ConfigService $configService = null,
        ?Logger        $logger = null
    )
    {
        if (!$configService) {
            $configService = $this->createMock(ConfigService::class);
        }

        if (!$logger) {
            $logger = $this->createMock(LoggerInterface::class);
        }
        return $this->apiFactory = new ApiFactory($configService,$logger);
    }

    /**
     * @covers ::__construct()
     * @return void
     */
    public function testConstruct()
    {
        $configService = $this->createMock(ConfigService::class);
        $logger = $this->createMock(LoggerInterface::class);
        $result = new ApiFactory($configService,$logger);
        $this->assertInstanceOf(ApiFactory::class,$result);
    }

    /**
     * @covers ::obfuscateApiKey
     * @return void
     */
    public function testObfuscateApiKey()
    {
        $this->createApiFactory();
        $notObfuscatedApiKey = 'notObfuscatedApiKey';
        $visibleCharacters = 6;
        $result = $this->apiFactory->obfuscateApiKey($notObfuscatedApiKey);
        $this->assertStringNotContainsString($notObfuscatedApiKey,$result);
        $this->assertStringStartsWith(substr($notObfuscatedApiKey, 0, $visibleCharacters),$result);

        $result = $this->apiFactory->obfuscateApiKey($notObfuscatedApiKey,0);
        $this->assertStringEndsWith($notObfuscatedApiKey,$result);
    }

    /**
     * @covers ::createClientForSalesChannel
     * @return void
     */
    public function testCreateClientForSalesChannel()
    {

        $context = $this->createMock(Context::class);

        $configStruct = $this->createMock(ConfigStruct::class);
        $configStruct->expects($this->exactly(2))
            ->method('isSandboxMode')
            ->willReturn(true);
        $configStruct->expects($this->once())
            ->method('getSandboxApiKey')
            ->willReturn('sandboxKey');
        $configStruct->expects($this->once())
            ->method('getCustomerData')
            ->willReturn($this->createMock(CustomerDataStruct::class));
        $configStruct->expects($this->once())
            ->method('getSenderAddress')
            ->willReturn($this->createMock(SenderAddressStruct::class));

        $configService = $this->createMock(ConfigService::class);
        $configService->expects($this->once())
            ->method('getConfiguration')
            ->with(
                $this->equalTo('mockedSalesChannelId'),
                $this->equalTo($context)
            )
            ->willReturn($configStruct);

        $this->createApiFactory($configService);

        $result = $this->apiFactory->createClientForSalesChannel('mockedSalesChannelId',$context);

        $this->assertInstanceOf(PostNL::class,$result);
    }

    /**
     * @covers ::createClient
     * @return void
     */
    public function testCreateClient()
    {
        $this->createApiFactory();

        $mockCustomerData = ['mockName'=>'name'];
        $mockSenderAddress = ['mockStreet'=>'street'];

        $result = $this->apiFactory->createClient('mockApiKey',false,$mockCustomerData,$mockSenderAddress);
        $this->assertInstanceOf(PostNL::class,$result);
    }
}
