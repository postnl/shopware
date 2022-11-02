<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Service\Shopware;

use Firstred\PostNL\Entity\Request\GetDeliveryDate;
use Firstred\PostNL\Entity\Response\GetDeliveryDateResponse;
use PostNL\Shopware6\Service\PostNL\ApiExtension\PostNLExtension;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DeliveryDateService
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

    public function getDeliveryDate(SalesChannelContext $context, GetDeliveryDate $getDeliveryDate): GetDeliveryDateResponse
    {
        /** @var PostNLExtension $apiClient */
        $apiClient = $this->apiFactory->createClientForSalesChannel($context->getSalesChannelId(), $context->getContext());
        return $apiClient->getDeliveryDate($getDeliveryDate);
    }
}
