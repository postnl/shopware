<?php

namespace PostNL\Shopware6\Facade;

use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\PostNL\PostalCodeService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class PostalCodeFacade
{
    /**
     * @var PostalCodeService
     */
    protected $postalCodeService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        PostalCodeService $postalCodeService,
        LoggerInterface $logger
    )
    {
        $this->postalCodeService = $postalCodeService;
        $this->logger = $logger;
    }

    public function checkPostalCode(SalesChannelContext $context, string $postalCode, string $houseNumber, string $houseNumberAddition = null)
    {
        try {
            $this->logger->debug("Checking postal code", [
                'postal code' => $postalCode,
                'house number' => $houseNumber,
                'house number addition' => $houseNumberAddition,
            ]);

            return $this->postalCodeService->checkPostalCode($context,$postalCode,$houseNumber,$houseNumberAddition);


        } catch (ResponseException|InvalidConfigurationException $e) {
            return false;
        }
    }
}
