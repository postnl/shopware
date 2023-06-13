<?php
declare(strict_types=1);

namespace PostNL\tests\Service\Shopware;

use PHPUnit\Framework\TestCase;
use PostNL\Shopware6\Service\Shopware\ShippingMethodService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Content\Rule\RuleEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

/**
 * @coversDefaultClass \PostNL\Shopware6\Service\Shopware\ShippingMethodService
 */
class ShippingMethodServiceTest extends TestCase
{

    private const NAMES = [
        'shipment' => [
            'en-GB' => 'PostNL standard shipping',
            'nl-NL' => 'PostNL standaard verzending',
            'de-DE' => 'PostNL Standardversand',
        ],
        'pickup' => [
            'en-GB' => 'Pickup at PostNL point',
            'nl-NL' => 'Ophalen bij PostNL-punt',
            'de-DE' => 'Abholung am PostNL-Punkt',
        ],
        'mailbox' => [
            'en-GB' => 'PostNL Mailbox parcel',
            'nl-NL' => 'PostNL Brievenbuspakje',
            'de-DE' => 'PostNL Mailbox parcel',
        ],
    ];

    private function createShippingMethodService(
        $deliveryTimeRepository = null,
        $mediaRepository = null,
        $ruleRepository = null,
        $shippingMethodRepository = null,
        $mediaService = null,
        $logger = null
    )
    {
        if (!$deliveryTimeRepository) {
            $deliveryTimeRepository = $this->createMock(EntityRepositoryInterface::class);
        }

        if (!$mediaRepository) {
            $mediaRepository = $this->createMock(EntityRepositoryInterface::class);
        }

        if (!$ruleRepository) {
            $ruleRepository = $this->createMock(EntityRepositoryInterface::class);
        }
        if (!$shippingMethodRepository) {
            $shippingMethodRepository = $this->createMock(EntityRepositoryInterface::class);
        }
        if (!$mediaService) {
            $mediaService = $this->createMock(MediaService::class);
        }
        if (!$logger) {
            $logger = $this->createMock(LoggerInterface::class);
        }

        return new ShippingMethodService(
            $deliveryTimeRepository,
            $mediaRepository,
            $ruleRepository,
            $shippingMethodRepository,
            $mediaService,
            $logger
        );
    }

  

    /**
     * @covers ::createShippingMethod()
     * @return void
     */
    public function testCreateShippingMethod()
    {
        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->expects($this->once())
            ->method('first')
            ->willReturn(null);

        $shippingMethodRepository = $this->createMock(EntityRepositoryInterface::class);
        $shippingMethodRepository->expects($this->once())
            ->method('search')
            ->with(
                $this->isInstanceOf(Criteria::class),
                $this->isInstanceOf(Context::class)
            )
            ->willReturn($searchResult);

        $context = $this->createMock(Context::class);
        $mockCurrencyId = 'mockCurrencyId';
        $context->expects($this->once())
            ->method('getCurrencyId')
            ->willReturn($mockCurrencyId);

        $deliveryType = 'shipment';//TODO: Loop this?
        $ruleId = 'mockRuleId';
        $deliveryTimeId = 'mockDeliveryTimeId';
        $mediaId = 'mockMediaId';

        $shippingMethodRepository->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($entity) use ($mockCurrencyId, $mediaId, $deliveryTimeId, $ruleId, $deliveryType) {
                    $entity = $entity[0];
                    $result = !empty($entity['id']);
                    $result &= $entity['name'] == self::NAMES[$deliveryType];
                    $result &= ($entity['active'] == false);
                    $result &= ($entity['availabilityRuleId'] == $ruleId);
                    $result &= ($entity['deliveryTimeId'] == $deliveryTimeId);
                    $result &= ($entity['mediaId'] == $mediaId);
                    $result &= ($entity['prices'][0]['currencyId'] == $mockCurrencyId);

                    return boolval($result);
                }),
                $this->equalTo($context)
            );
        $shippingMethodService = $this->createShippingMethodService(
            null,
            null,
            null,
            $shippingMethodRepository
        );
        $shippingMethodService->createShippingMethod(
            $deliveryType,
            $ruleId,
            $deliveryTimeId,
            $mediaId,
            $context
        );
    }

    /**
     * @covers ::createShippingMethod()
     * @return void
     */
    public function testCreateShippingMethodError()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error');


        $event = $this->createMock(EntityWrittenContainerEvent::class);
        $event->expects($this->exactly(3))
            ->method('getErrors')
            ->willReturn(['error']);

        $shippingMethodRepository = $this->createMock(EntityRepositoryInterface::class);
        $shippingMethodRepository->expects($this->once())
            ->method('create')
            ->willReturn($event);

        $context = $this->createMock(Context::class);

        $shippingMethodService = $this->createShippingMethodService(
            null,
            null,
            null,
            $shippingMethodRepository,
            null,
            $logger
        );
        $shippingMethodService->createShippingMethod(
            "",
            "",
            "",
            "",
            $context
        );
    }

    /**
     * @covers ::createShippingMethod()
     * @covers ::getShippingMethodForType()
     * @return void
     */
    public function testCreateExistingShippingMethod()
    {
        $mockId = 'mockId';

        $shippingMethodEntity = $this->createMock(ShippingMethodEntity::class);
        $shippingMethodEntity->expects($this->once())
            ->method('getId')
            ->willReturn($mockId);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->expects($this->once())
            ->method('first')
            ->willReturn($shippingMethodEntity);

        $shippingMethodRepository = $this->createMock(EntityRepositoryInterface::class);
        $shippingMethodRepository->expects($this->once())
            ->method('search')
            ->with(
                $this->isInstanceOf(Criteria::class),
                $this->isInstanceOf(Context::class)
            )
            ->willReturn($searchResult);

        $shippingMethodService = $this->createShippingMethodService(
            null,
            null,
            null,
            $shippingMethodRepository
        );

        $deliveryType = 'mockType';
        $context = $this->createMock(Context::class);

        $result = $shippingMethodService->createShippingMethod($deliveryType, "", "", "", $context);

        $this->assertEquals($mockId, $result);

    }

    /**
     * @covers ::__construct()
     * @return void
     */
    public function test__construct()
    {
        $shippingMethodService = $this->createShippingMethodService();
        $this->assertInstanceOf(ShippingMethodService::class, $shippingMethodService);
    }
}
