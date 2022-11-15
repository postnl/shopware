<?php
declare(strict_types=1);

namespace PostNL\tests\Service\Shopware;

use Firstred\PostNL\Entity\Response\ResponseTimeframes;
use Firstred\PostNL\PostNL;
use PHPUnit\Framework\TestCase;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\Shopware\TimeframeService;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Firstred\PostNL\Entity\Request\GetTimeframes;

class TimeframeServiceTest extends TestCase
{
    private TimeframeService $timeframeService;

    private function createTimeFrameService(?ApiFactory $apiFactory = null
    )
    {
        if (!$apiFactory) {
            $apiFactory = $this->createMock(ApiFactory::class);
        }

        return $this->timeframeService = new TimeframeService($apiFactory);
    }

    public function testGetTimeframes()
    {
        $getTimeframesResponse = $this->createMock(ResponseTimeframes::class);
        $getTimeframes = $this->createMock(GetTimeframes::class);


        $apiClient = $this->createMock(PostNL::class);
        $apiClient->expects($this->once())
            ->method('getTimeframes')
            ->with($this->equalTo($getTimeframes))
            ->willReturn($getTimeframesResponse);

        $timeFrameServiceInterface = $this->createConfiguredMock(TimeframeService::class,
            [
                'getTimeframes'=>$getTimeframesResponse
            ]
        );

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

        $this->createTimeFrameService($apiFactory);

        $result = $this->timeframeService->getTimeframes($salesChannelContext, $getTimeframes);


        $this->assertEquals($getTimeframesResponse, $result);
    }
}
