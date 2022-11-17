<?php
declare(strict_types=1);

namespace PostNL\tests\Service\Shopware;

use PostNL\Shopware6\Exception\Attribute\MissingAttributeStructException;
use PostNL\Shopware6\Exception\Attribute\MissingPropertyAccessorMethodException;
use PostNL\Shopware6\Exception\Attribute\MissingReturnTypeException;
use PostNL\Shopware6\Exception\Attribute\MissingTypeHandlerException;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PHPUnit\Framework\TestCase;

use PostNL\Shopware6\Struct\Config\ConfigStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\ShopwareHttpException;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @coversDefaultClass \PostNL\Shopware6\Service\Shopware\ConfigService
 */
class ConfigServiceTest extends TestCase
{
    protected ConfigService $configService;

    private function createConfigService(
        SystemConfigService $configService = null,
        AttributeFactory    $attributeFactory = null,
        LoggerInterface     $logger = null
    )
    {
        if (!$configService)
            $configService = $this->createMock(SystemConfigService::class);
        if (!$attributeFactory)
            $attributeFactory = $this->createMock(AttributeFactory::class);
        if (!$logger)
            $logger = $this->createMock(LoggerInterface::class);

        $this->configService = new ConfigService($configService, $attributeFactory, $logger);
    }

    /**
     * @covers ::__construct
     * @return void
     */
    public function test__construct()
    {
        $this->createConfigService();
        $this->assertInstanceOf(ConfigService::class, $this->configService);
    }

    /**
     * @covers ::getConfiguration
     * @return void
     */
    public function testGetConfiguration()
    {
        $salesChannelId = null;
        $context = $this->createMock(Context::class);
        $config = [ConfigService::DOMAIN . 'mockKey' => 'mockValue'];
        $configService = $this->createMock(SystemConfigService::class);
        $configService->expects($this->once())
            ->method('getDomain')
            ->willReturn($config);

        $attributeFactory = $this->createMock(AttributeFactory::class);
        $mockConfigStruct = $this->createMock(ConfigStruct::class);
        $attributeFactory->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo(ConfigStruct::class),
                $this->equalTo(['mockKey' => 'mockValue']),
                $this->equalTo($context)
            )
            ->willReturn($mockConfigStruct);
        $logger = $this->createMock(LoggerInterface::class);

        $logger->expects($this->once())
            ->method('debug');

        $this->createConfigService($configService, $attributeFactory, $logger);
        $result = $this->configService->getConfiguration($salesChannelId, $context);
        $this->assertEquals($mockConfigStruct, $result);
    }

    public function testAllGetConfigurationExceptions()
    {
        $this->testGetConfigurationExceptions(new MissingAttributeStructException());
        $this->testGetConfigurationExceptions(new MissingPropertyAccessorMethodException());
        $this->testGetConfigurationExceptions(new MissingReturnTypeException());
        $this->testGetConfigurationExceptions(new MissingTypeHandlerException());
    }

    private function testGetConfigurationExceptions(ShopwareHttpException $exception)
    {

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('critical');

        $context = $this->createMock(Context::class);

        $attributeFactory = $this->createMock(AttributeFactory::class);
        $attributeFactory->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo(ConfigStruct::class),
                $this->anything(),
                $this->equalTo($context)
            )
            ->willThrowException($exception);

        $this->createConfigService(null, $attributeFactory, $logger);

        $this->expectExceptionObject($exception);

        $salesChannelId = null;
        $this->configService->getConfiguration($salesChannelId, $context);
    }
}
