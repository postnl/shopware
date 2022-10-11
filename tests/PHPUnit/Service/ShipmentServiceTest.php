<?php

namespace PostNL\tests\Service;

use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Entity\Product\ProductEntity;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\Builder\ShipmentBuilder;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\PostNL\Label\Extractor\LabelExtractorInterface;
use PostNL\Shopware6\Service\PostNL\Label\Label;
use PostNL\Shopware6\Service\PostNL\Label\LabelDefaults;
use PostNL\Shopware6\Service\PostNL\Label\MergedLabelResponse;
use PostNL\Shopware6\Service\PostNL\LabelService;
use PostNL\Shopware6\Service\PostNL\Product\ProductService;
use PostNL\Shopware6\Service\PostNL\ShipmentService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shopware6\Service\Shopware\OrderService;
use PostNL\Shopware6\Struct\Attribute\OrderAttributeStruct;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\Country\CountryEntity;

class ShipmentServiceTest extends TestCase
{

    private ShipmentService $shipmentService;

    protected function setUp(): void
    {

    }

    /**
     * @param ApiFactory|null $apiFactory
     * @param OrderDataExtractor|null $orderDataExtractor
     * @param OrderService|null $orderService
     * @param ConfigService|null $configService
     * @param LabelService|null $labelService
     * @param ShipmentBuilder|null $shipmentBuilder
     * @param LabelExtractorInterface|null $labelExtractor
     * @param AttributeFactory|null $attributeFactory
     * @param ProductService|null $productService
     * @return ShipmentService
     */
    private function createShipmentService(
        ApiFactory              $apiFactory = null,
        OrderDataExtractor      $orderDataExtractor = null,
        OrderService            $orderService = null,
        ConfigService           $configService = null,
        LabelService            $labelService = null,
        ShipmentBuilder         $shipmentBuilder = null,
        LabelExtractorInterface $labelExtractor = null,
        AttributeFactory        $attributeFactory = null,
        ProductService          $productService = null
    )
    {
        if (!$apiFactory) {
            $apiFactory = $this->createMock(ApiFactory::class);
        }
        if (!$orderDataExtractor) {
            $orderDataExtractor = $this->createMock(OrderDataExtractor::class);
        }
        if (!$orderService) {
            $orderService = $this->createMock(OrderService::class);
        }
        if (!$configService) {
            $configService = $this->createMock(ConfigService::class);
        }
        if (!$labelService) {
            $labelService = $this->createMock(LabelService::class);
        }
        if (!$shipmentBuilder) {
            $shipmentBuilder = $this->createMock(ShipmentBuilder::class);
        }
        if (!$labelExtractor) {
            $labelExtractor = $this->createMock(LabelExtractorInterface::class);
        }
        if (!$attributeFactory) {
            $attributeFactory = $this->createMock(AttributeFactory::class);
        }
        if (!$productService) {
            $productService = $this->createMock(ProductService::class);
        }

        return $this->shipmentService = new ShipmentService(
            $apiFactory,
            $orderDataExtractor,
            $orderService,
            $configService,
            $labelService,
            $shipmentBuilder,
            $labelExtractor,
            $attributeFactory,
            $productService
        );
    }

    protected function generate(array $yield_values): \Generator
    {
        yield from $yield_values;
    }

    public function testGenerateBarcodesForOrders()
    {
        $apiClient = $this->createConfiguredMock(
            \Firstred\PostNL\PostNL::class,
            ['generateBarcodesByCountryCodes' => ['NL' => ['3SDEVC873511680']]]
        );

        $apiFactory = $this->createMock(ApiFactory::class);

        $apiFactory->expects($this->once())->method('createClientForSalesChannel')->willReturn($apiClient);

        $mockedCountry = $this->createConfiguredMock(
            CountryEntity::class,
            [
                'getIso' => 'NL'
            ]
        );

        $orderDataExtractor = $this->createConfiguredMock(
            OrderDataExtractor::class,
            [
                'extractDeliveryCountry' => $mockedCountry
            ]
        );

        $this->shipmentService = $this->createShipmentService(
            $apiFactory,
            $orderDataExtractor,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );

        $iterableOrders = $this->getIterableOrders();

        $orders = $this->createConfiguredMock(OrderCollection::class,
            [
                'getSalesChannelIs' => ['foo'],
                'filterBySalesChannelId' => $iterableOrders
            ]);
        $actualResult = $this->shipmentService->generateBarcodesForOrders(
            $orders,
            Context::createDefaultContext()
        );

        $this->assertNotNull($actualResult);
        $this->assertIsArray($actualResult);
        $this->assertEquals([12345 => "3SDEVC873511680"], $actualResult);
    }

    public function testEmptyShipOrders()
    {
        $this->shipmentService = $this->createShipmentService(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );

        $orders = $this->createConfiguredMock(OrderCollection::class,
            [
            ]);

        $actualResult = $this->shipmentService->shipOrders($orders, false, Context::createDefaultContext());

        $this->assertInstanceOf(MergedLabelResponse::class, $actualResult);
        $this->assertEquals('pdf', $actualResult->getType());
        $this->assertEquals('', $actualResult->getContent());
    }

    public function testGlobalPackShipOrders()
    {
        $orderAttributes = $this->createConfiguredMock(OrderAttributeStruct::class, ['getProductId' => '5']);
        $attributeFactory = $this->createConfiguredMock(
            AttributeFactory::class,
            ['createFromEntity' => $orderAttributes]
        );

        $product = $this->createConfiguredMock(
            ProductEntity::class,
            ['getId' => Defaults::PRODUCT_SHIPPING_NL_GLOBAL_4945]
        );

        $productService = $this->createConfiguredMock(
            ProductService::class,
            ['getProduct' => $product]
        );

        $config = $this->createConfiguredMock(
            ConfigStruct::class,
            ['getPrinterFormat' => 'a9000']
        );

        $configService = $this->createConfiguredMock(
            ConfigService::class,
            ['getConfiguration' => $config]
        );

        $shipmentBuilder = $this->createMock(
            ShipmentBuilder::class
        );

        $orderService = $this->createMock(OrderService::class);
        $orderService->expects($this->once())->method('updateOrderCustomFields');

        $labelExtractor = $this->createConfiguredMock(
            LabelExtractorInterface::class,
            [
                'extract' => [$this->createMock(Label::class)]
            ]
        );
        $labelExtractor->expects($this->once())->method('extract');

        $labelService = $this->createMock(LabelService::class);
        $labelService->expects($this->once())->method('mergeLabels')
            ->with(
                $this->isType(IsType::TYPE_ARRAY),
                $this->equalTo([]),
                $this->equalTo(LabelDefaults::LABEL_FORMAT_A4)
            );

        $this->shipmentService = $this->createShipmentService(
            null,
            null,
            $orderService,
            $configService,
            $labelService,
            $shipmentBuilder,
            $labelExtractor,
            $attributeFactory,
            $productService
        );

        $iterableOrders = $this->getIterableOrders();

        $orders = $this->createConfiguredMock(OrderCollection::class,
            [
                'getSalesChannelIs' => ['foo'],
                'filterBySalesChannelId' => $iterableOrders
            ]);


        $actualResult = $this->shipmentService->shipOrders($orders, true, Context::createDefaultContext());

        $this->assertInstanceOf(MergedLabelResponse::class, $actualResult);
        $this->assertEquals('pdf', $actualResult->getType());
        $this->assertEquals('', $actualResult->getContent());
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|OrderCollection|OrderCollection&\PHPUnit\Framework\MockObject\MockObject
     */
    private function getIterableOrders()
    {
        $order = $this->createConfiguredMock(OrderEntity::class,
            [
                'getId' => '12345'
            ]);

        $iterableOrders = $this->createConfiguredMock(OrderCollection::class,
            [
                'getIterator' => $this->generate([$order])
            ]
        );
        return $iterableOrders;
    }
}
