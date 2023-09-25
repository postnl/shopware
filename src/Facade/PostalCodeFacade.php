<?php declare(strict_types=1);

namespace PostNL\Shopware6\Facade;



use Firstred\PostNL\Exception\CifDownException;
use Firstred\PostNL\Exception\CifException;
use Firstred\PostNL\Exception\HttpClientException;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use PostNL\Shopware6\Component\PostNL\Entity\Response\PostalCodeResponse;
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
        $this->logger->debug("Checking postal code", [
            'postal code' => $postalCode,
            'house number' => $houseNumber,
            'house number addition' => $houseNumberAddition,
        ]);

        return $this->postalCodeService->checkPostalCode($context, $postalCode, $houseNumber, $houseNumberAddition);

    }
}
