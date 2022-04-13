<?php

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PostalCodeService
{
    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    const DOMAIN_NAMESPACE = 'http://postnl.nl/cif/domain/PostalCodeService/';

    public function __construct(ApiFactory $apiFactory)
    {
        $this->apiFactory = $apiFactory;
    }

    /**
     * @param SalesChannelContext $context
     * @param string $postalCode
     * @param string $houseNumber
     * @param string|null $houseNumberAddition
     * @return ApiExtension\Entity\Response\PostalCodeResponse
     * @throws ApiExtension\Exception\InvalidAddressException|ApiExtension\Exception\AddressNotFoundException
     */
    public function checkPostalCode(SalesChannelContext $context, string $postalCode, string $houseNumber, string $houseNumberAddition = null): ApiExtension\Entity\Response\PostalCodeResponse
    {
        $apiClient = $this->apiFactory->createClientForSalesChannel($context->getSalesChannelId(), $context->getContext());

        return $apiClient->getPostalCode($postalCode,$houseNumber,$houseNumberAddition);
    }
}
