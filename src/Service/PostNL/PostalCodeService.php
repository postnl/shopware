<?php

namespace PostNL\Shopware6\Service\PostNL;

use Firstred\PostNL\Exception\CifDownException;
use Firstred\PostNL\Exception\CifException;
use Firstred\PostNL\Exception\HttpClientException;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use PostNL\Shopware6\Component\PostNL\Entity\Response\PostalCodeResponse;
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
     * @param int                 $houseNumber
     * @param string|null         $houseNumberAddition
     * @return PostalCodeResponse
     * @throws CifDownException
     * @throws CifException
     * @throws HttpClientException
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     * @throws ResponseException
     */
    public function checkPostalCode(SalesChannelContext $context, string $postalCode, int $houseNumber, string $houseNumberAddition = null): PostalCodeResponse
    {
        $apiClient = $this->apiFactory->createClientForSalesChannel($context->getSalesChannelId(), $context->getContext());

        return $apiClient->getPostalCode($postalCode, $houseNumber, $houseNumberAddition);
    }
}
