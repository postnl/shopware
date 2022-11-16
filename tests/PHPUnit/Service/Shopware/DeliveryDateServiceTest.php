<?php
declare(strict_types=1);

namespace PostNL\tests\Service\Shopware;

use Firstred\PostNL\Entity\Request\GetDeliveryDate;
use Firstred\PostNL\Entity\Response\GetDeliveryDateResponse;
use Firstred\PostNL\PostNL;
use PHPUnit\Framework\TestCase;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\Shopware\DeliveryDateService;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * @coversDefaultClass \PostNL\Shopware6\Service\Shopware\DeliveryDateService
 */
class DeliveryDateServiceTest extends TestCase
{
    private DeliveryDateService $deliveryDateService;

    private function createDeliveryDateService(?ApiFactory $apiFactory = null
    )
    {
        if (!$apiFactory) {
            $apiFactory = $this->createMock(ApiFactory::class);
        }

        return $this->deliveryDateService = new DeliveryDateService($apiFactory);
    }

    /**
     * @covers ::__construct()
     * @return void
     */
    public function testConstruct()
    {
        $apiFactory = $this->createMock(ApiFactory::class);
        $result = new DeliveryDateService($apiFactory);
        $this->assertInstanceOf(DeliveryDateService::class,$result);
    }

    /**
     * @covers ::getDeliveryDate
     * @return void
     */
    public function testGetDeliveryDate()
    {
        $getDeliveryDateResponse = $this->createMock(GetDeliveryDateResponse::class);
        $getDeliveryDate = $this->createMock(GetDeliveryDate::class);


        $apiClient = $this->createMock(PostNL::class);
        $apiClient->expects($this->once())
            ->method('getDeliveryDate')
            ->with($this->isInstanceOf(GetDeliveryDate::class))
            ->willReturn($getDeliveryDateResponse);


        $context = $this->createMock(Context::class);
        $salesChannelContext = $this->createConfiguredMock(SalesChannelContext::class,
            [
                'getSalesChannelId' => 'mockedSalesChannelId',
                'getContext' => $context
            ]
        );

        $apiFactory = $this->createMock(ApiFactory::class);
        $apiFactory->expects($this->once())
            ->method('createClientForSalesChannel')
            ->with(
                $this->equalTo('mockedSalesChannelId'),
                $this->equalTo($context)
            )
            ->willReturn($apiClient);

        $this->createDeliveryDateService($apiFactory);

        $result = $this->deliveryDateService->getDeliveryDate($salesChannelContext, $getDeliveryDate);


        $this->assertEquals($getDeliveryDateResponse, $result);
    }
}
