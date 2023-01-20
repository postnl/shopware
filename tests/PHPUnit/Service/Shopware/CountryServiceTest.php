<?php
declare(strict_types=1);

namespace PostNL\tests\Service\Shopware;

use PostNL\Shopware6\Exception\Shopware\InvalidCountryIdException;
use PostNL\Shopware6\Service\Shopware\CountryService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\Country\CountryEntity;

/**
 * @coversDefaultClass \PostNL\Shopware6\Service\Shopware\CountryService
 */
class CountryServiceTest extends TestCase
{
    protected CountryService $countryService;

    private function createCountryService(
        EntityRepositoryInterface $countryRepository = null,
        LoggerInterface           $logger = null
    )
    {
        if (!$countryRepository) {
            $countryRepository = $this->createMock(EntityRepositoryInterface::class);
        }
        if (!$logger) {
            $logger = $this->createMock(LoggerInterface::class);
        }

        return new CountryService($countryRepository, $logger);
    }

    /**
     * @covers ::__construct
     * @return void
     */
    public function test__construct()
    {
        $countryRepository = $this->createMock(EntityRepositoryInterface::class);
        $logger = $this->createMock(LoggerInterface::class);
        $result = new CountryService($countryRepository, $logger);
        $this->assertInstanceOf(CountryService::class, $result);
    }

    /**
     * @covers ::getCountryCodeById
     * @return void
     */
    public function testGetCountryCodeById()
    {
        $context = $this->createMock(Context::class);
        $countryRepository = $this->createMock(EntityRepositoryInterface::class);
        $entitySearchResult = $this->createMock(EntitySearchResult::class);
        $countryEntity = $this->createMock(CountryEntity::class);
        $mockedIso = 'mockedIso';

        $countryEntity->expects($this->once())
            ->method('getIso')
            ->willReturn($mockedIso);

        $entitySearchResult->expects($this->once())
            ->method('first')
            ->willReturn($countryEntity);

        $countryRepository->expects($this->once())
            ->method('search')
            ->with(
                $this->isInstanceOf(Criteria::class),
                $this->equalTo($context)
            )
            ->willReturn($entitySearchResult);

        $this->countryService = $this->createCountryService($countryRepository);

        $result = $this->countryService->getCountryCodeById('mockedId', $context);

        $this->assertEquals($mockedIso, $result);

    }

    /**
     * @covers ::getCountryById
     * @return void
     */
    public function testGetCountryById()
    {
        $context = $this->createMock(Context::class);
        $countryRepository = $this->createMock(EntityRepositoryInterface::class);
        $entitySearchResult = $this->createMock(EntitySearchResult::class);
        $countryEntity = $this->createMock(CountryEntity::class);

        $entitySearchResult->expects($this->once())
            ->method('first')
            ->willReturn($countryEntity);

        $countryRepository->expects($this->once())
            ->method('search')
            ->with(
                $this->isInstanceOf(Criteria::class),
                $this->equalTo($context)
            )
            ->willReturn($entitySearchResult);

        $this->countryService = $this->createCountryService($countryRepository);

        $result = $this->countryService->getCountryById('od', $context);
        $this->assertEquals($countryEntity, $result);

    }

    /**
     * @return void
     * @covers ::getCountryById
     */
    public function testGetCountryByIdException()
    {

        $context = $this->createMock(Context::class);
        $entitySearchResult = $this->createMock(EntitySearchResult::class);
        $entitySearchResult->expects($this->once())
            ->method('first')
            ->willReturn(null);

        $countryRepository = $this->createMock(EntityRepositoryInterface::class);
        $countryRepository->expects($this->once())
            ->method('search')
            ->with(
                $this->isInstanceOf(Criteria::class),
                $this->equalTo($context)
            )
            ->willReturn($entitySearchResult);

        $this->countryService = $this->createCountryService($countryRepository);
        $countryId = 'mockCountryId';

        $this->expectExceptionObject(new InvalidCountryIdException([
                'countryId' => $countryId
            ])
        );

        $this->countryService->getCountryById($countryId, $context);
    }
}
