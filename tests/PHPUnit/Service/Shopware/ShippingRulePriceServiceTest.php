<?php
declare(strict_types=1);

namespace PostNL\tests\Service\Shopware;

use PostNL\Shopware6\Defaults as PostNLDefaults;
use PostNL\Shopware6\Service\Shopware\ShippingRulePriceService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;

class ShippingRulePriceServiceTest extends TestCase
{

//    public function testCreatePricingMatrix()
//    {
//
//    }

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
        
        $shippingMethodArray = ['shipment' => $mockId,'pickup' => $mockId];

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

//    public function test__construct()
//    {
//
//    }
}
