<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Service\Shopware;

use Firstred\PostNL\Entity\Request\GetTimeframes;
use Firstred\PostNL\Entity\Response\ResponseTimeframes;
use PostNL\Shopware6\Component\PostNL\PostNL;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class TimeframeService
{
    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    public function __construct(
        ApiFactory $apiFactory
    )
    {
        $this->apiFactory = $apiFactory;
    }

    public function getTimeframes(SalesChannelContext $context, GetTimeframes $getTimeframes): ResponseTimeframes
    {
        /** @var PostNL $apiClient */
        $apiClient = $this->apiFactory->createClientForSalesChannel($context->getSalesChannelId(), $context->getContext());
        return $apiClient->getTimeframes($getTimeframes);
    }
}
