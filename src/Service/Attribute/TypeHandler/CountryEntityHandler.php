<?php

namespace PostNL\Shopware6\Service\Attribute\TypeHandler;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Country\CountryEntity;

class CountryEntityHandler implements AttributeTypeHandlerInterface
{
    /**
     * @var EntityRepository
     */
    protected $countryRepository;

    public function __construct(EntityRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @inheritDoc
     */
    public function supports(): array
    {
        return [CountryEntity::class];
    }

    /**
     * @inheritDoc
     */
    public function handle($data, Context $context): ?CountryEntity
    {
        /** @var CountryEntity|null $country */
        $country = $this->countryRepository->search(new Criteria([$data]), $context)->first();

        return $country;
    }
}
