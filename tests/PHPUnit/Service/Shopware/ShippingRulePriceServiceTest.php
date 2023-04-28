<?php
declare(strict_types=1);

namespace PostNL\tests\Service\Shopware;

use PostNL\Shopware6\Defaults as PostNLDefaults;
use PostNL\Shopware6\Service\Shopware\ShippingRulePriceService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Shipping\Aggregate\ShippingMethodPrice\ShippingMethodPriceEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;


/**
 * @coversDefaultClass \PostNL\Shopware6\Service\Shopware\ShippingRulePriceService
 */
class ShippingRulePriceServiceTest extends TestCase
{

    private function createShippingRulePriceService(
        EntityRepositoryInterface $shippingMethodPricesRepository = null,
        LoggerInterface           $logger = null
    )
    {
        if (!$shippingMethodPricesRepository) {
            $shippingMethodPricesRepository = $this->createMock(EntityRepositoryInterface::class);
        }

        if (!$logger) {
            $logger = $this->createMock(LoggerInterface::class);
        }

        return new ShippingRulePriceService($shippingMethodPricesRepository, $logger);
    }

    /**
     * @covers ::__construct()
     * @return void
     */
    public function test__construct()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $shippingRepository = $this->createMock(EntityRepositoryInterface::class);
        $priceService = $this->createShippingRulePriceService($shippingRepository, $logger);
        $this->assertInstanceOf(ShippingRulePriceService::class, $priceService);

    }

    /**
     * @covers ::createPricingMatrix()
     * @return void
     */
    public function testCreatePricingMatrixExists()
    {
        $context = $this->createMock(Context::class);

        $shippingMethodEntity = $this->createMock(ShippingMethodPriceEntity::class);
        $shippingMethodEntity->expects($this->once())
            ->method('getId')
            ->willReturn('existingMockId');


        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->expects($this->once())
            ->method('getTotal')
            ->willReturn(1);
        $searchResult->expects($this->once())
            ->method('first')
            ->willReturn($shippingMethodEntity);

        $shippingMethodPricesRepository = $this->createMock(EntityRepositoryInterface::class);
        $shippingMethodPricesRepository->expects($this->once())
            ->method('search')
            ->with(
                $this->isInstanceOf(Criteria::class),
                $this->equalTo($context)
            )
            ->willReturn($searchResult);
        $priceService = $this->createShippingRulePriceService($shippingMethodPricesRepository, null);

        $result = $priceService->createPricingMatrix('mockMethodId', 'mockRuleId', $context);
        $this->assertEquals('existingMockId', $result);
    }

    /**
     * @covers ::createPricingMatrix()
     * @return void
     */
    public function testCreatePricingMatrixNew()
    {
        $context = $this->createMock(Context::class);

        $shippingMethodEntity = $this->createMock(ShippingMethodPriceEntity::class);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->expects($this->exactly(2))
            ->method('getTotal')
            ->willReturn(0);

        $shippingMethodPricesRepository = $this->createMock(EntityRepositoryInterface::class);
        $shippingMethodPricesRepository->expects($this->exactly(2))
            ->method('search')
            ->with(
                $this->isInstanceOf(Criteria::class),
                $this->equalTo($context)
            )
            ->willReturn($searchResult);


        $currencyPrice = ['c' . Defaults::CURRENCY => [
            'net' => '0',
            'gross' => '0',
            'linked' => false,
            'currencyId' => Defaults::CURRENCY
        ]
        ];

        $shippingMethodId = 'shippingMethodId';
        $ruleId = 'mockRuleId';

        $goodResult = $this->createMock(EntityWrittenContainerEvent::class);
        $badResult = $this->createMock(EntityWrittenContainerEvent::class);

        $badResult->expects($this->once())
            ->method('getErrors')
            ->willReturn(['Error']);


        $shippingMethodPricesRepository->expects($this->exactly(2))
            ->method('create')
            ->with(
                $this->callback(function ($entity) use ($ruleId, $shippingMethodId, $currencyPrice) {
                    $entity = $entity[0];

                    return $entity['calculation'] == 2 &&
                        $entity['quantityStart'] == 1 &&
                        $entity['currencyPrice'] == $currencyPrice &&
                        $entity['shippingMethodId'] == $shippingMethodId &&
                        $entity['ruleId'] == $ruleId &&
                        $entity['price'] == 0;
                }),
                $this->equalTo($context)
            )
        ->willReturnOnConsecutiveCalls($goodResult,$badResult);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with(
                $this->equalTo('Could not create Shipping method price'),
                $this->equalTo([
                    'shippingMethodId' => $shippingMethodId,
                    'ruleId' => $ruleId,
                    'result' => $badResult
                ])
            );

        $priceService = $this->createShippingRulePriceService($shippingMethodPricesRepository, $logger);

        $result = $priceService->createPricingMatrix($shippingMethodId, $ruleId, $context);
        $this->assertIsString($result);

        //Test error
        $result = $priceService->createPricingMatrix($shippingMethodId, $ruleId, $context);
        $this->assertNull($result);
    }

    /**
     * @covers ::createPricingMatrices()
     * @return void
     */
    public function testCreatePricingMatrices()
    {
        $context = $this->createMock(Context::class);
        $mockId = "mockId";

        $mockRulesIds = [
            PostNLDefaults::ZONE_ONLY_NETHERLANDS => 'mockNL',
            PostNLDefaults::ZONE_ONLY_BELGIUM => 'mockBE',
            PostNLDefaults::ZONE_ONLY_EUROPE => 'mockEU',
            PostNLDefaults::ZONE_ONLY_REST_OF_WORLD => 'mockROW',
        ];

        $shippingMethodArray = ['shipment' => $mockId, 'pickup' => $mockId];

        $priceService = $this->createPartialMock(
            ShippingRulePriceService::class,
            ['createPricingMatrix']
        );
        $priceService->expects(
            $this->exactly(6))
            ->method('createPricingMatrix')
            ->withConsecutive(
                [
                    $this->equalTo($mockId),
                    $this->equalTo('mockNL'),
                    $this->equalTo($context)
                ],
                [
                    $this->equalTo($mockId),
                    $this->equalTo('mockBE'),
                    $this->equalTo($context)
                ],
                [
                    $this->equalTo($mockId),
                    $this->equalTo('mockEU'),
                    $this->equalTo($context)
                ],
                [
                    $this->equalTo($mockId),
                    $this->equalTo('mockROW'),
                    $this->equalTo($context)
                ],
                [
                    $this->equalTo($mockId),
                    $this->equalTo('mockNL'),
                    $this->equalTo($context)
                ],
                [
                    $this->equalTo($mockId),
                    $this->equalTo('mockBE'),
                    $this->equalTo($context)
                ]
            );

        $priceService->createPricingMatrices($shippingMethodArray, $mockRulesIds, $context);

    }
}
