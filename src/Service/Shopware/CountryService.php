<?php

namespace PostNL\Shopware6\Service\Shopware;

use PostNL\Shopware6\Exception\Shopware\InvalidCountryIdException;
use PostNL\Shopware6\Exception\Shopware\InvalidCountryIsoException;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\Country\CountryEntity;

class CountryService
{
    /**
     * @var EntityRepositoryInterface
     */
    protected $countryRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        EntityRepositoryInterface $countryRepository,
        LoggerInterface           $logger
    )
    {
        $this->countryRepository = $countryRepository;
        $this->logger = $logger;
    }

    /**
     * @param string  $countryId
     * @param Context $context
     * @return CountryEntity
     * @throws \Exception
     */
    public function getCountryById(string $countryId, Context $context): CountryEntity
    {
        $this->logger->debug('Getting country', [
            'countryId' => $countryId,
        ]);

        $country = $this->countryRepository->search((new Criteria([$countryId])), $context)->first();

        if ($country instanceof CountryEntity) {
            return $country;
        }

        throw new InvalidCountryIdException([
            'countryId' => $countryId,
        ]);
    }

    /**
     * @param string  $countryId
     * @param Context $context
     * @return string
     * @throws \Exception
     */
    public function getCountryCodeById(string $countryId, Context $context): string
    {
        $this->logger->debug("Getting country ISO code", [
            'countryId' => $countryId,
        ]);

        $country = $this->getCountryById($countryId, $context);

        return $country->getIso();
    }

    public function getCountryByIso(string $isoCode, Context $context): CountryEntity
    {
        $this->logger->debug("Getting country for ISO code", [
            'isoCode' => $isoCode,
        ]);

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('iso', $isoCode));

        $country = $this->countryRepository->search($criteria, $context)->first();

        if ($country instanceof CountryEntity) {
            return $country;
        }

        throw new InvalidCountryIsoException([
            'isoCode' => $isoCode,
        ]);
    }
}
