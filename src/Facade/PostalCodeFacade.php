<?php declare(strict_types=1);

namespace PostNL\Shopware6\Facade;

use PostNL\Shopware6\Service\PostNL\Api\Entity\Response\PostalCodeResponse;
use PostNL\Shopware6\Service\PostNL\Api\Exception\AddressNotFoundException;
use PostNL\Shopware6\Service\PostNL\Api\Exception\InvalidAddressException;
use PostNL\Shopware6\Service\PostNL\PostalCodeService;
use Psr\Log\LoggerInterface;
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
        LoggerInterface   $logger
    )
    {
        $this->postalCodeService = $postalCodeService;
        $this->logger = $logger;
    }

    /**
     * @param SalesChannelContext $context
     * @param string $postalCode
     * @param string $houseNumber
     * @param string|null $houseNumberAddition
     * @return PostalCodeResponse
     * @throws InvalidAddressException|AddressNotFoundException
     */
    public function checkPostalCode(SalesChannelContext $context, string $postalCode, string $houseNumber, string $houseNumberAddition = null): PostalCodeResponse
    {

        $this->logger->debug("Checking postal code", [
            'postal code' => $postalCode,
            'house number' => $houseNumber,
            'house number addition' => $houseNumberAddition,
        ]);
        return $this->postalCodeService->checkPostalCode($context, $postalCode, $houseNumber, $houseNumberAddition);

    }
}
