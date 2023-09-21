<?php

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Service\PostNL\Api\PostNL;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PostalCodeService
{
    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    public function __construct(ApiFactory $apiFactory)
    {
        $this->apiFactory = $apiFactory;
    }

    /**
     * @param SalesChannelContext $context
     * @param string              $postalCode
     * @param string              $houseNumber
     * @param string|null         $houseNumberAddition
     * @return Api\Entity\Response\PostalCodeResponse
     * @throws Api\Exception\InvalidAddressException|Api\Exception\AddressNotFoundException
     */
    public function checkPostalCode(SalesChannelContext $context, string $postalCode, string $houseNumber, string $houseNumberAddition = null): Api\Entity\Response\PostalCodeResponse
    {
        /** @var PostNL $apiClient */
        $apiClient = $this->apiFactory->createClientForSalesChannel($context->getSalesChannelId(), $context->getContext());

        return $apiClient->getPostalCode($postalCode, $houseNumber, $houseNumberAddition);
    }
}
