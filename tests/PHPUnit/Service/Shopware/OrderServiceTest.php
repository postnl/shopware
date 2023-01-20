<?php
declare(strict_types=1);

namespace PostNL\tests\Service\Shopware;

use Closure;
use phpDocumentor\Reflection\Types\This;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Shopware\OrderService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

/**
 * @coversDefaultClass \PostNL\Shopware6\Service\Shopware\OrderService
 */
class OrderServiceTest extends TestCase
{
    protected OrderService $orderService;

    private function createOrderService(EntityRepositoryInterface $orderRepository = null)
    {
        if (!$orderRepository) {
            $orderRepository = $this->createMock(EntityRepositoryInterface::class);
        }
        $this->orderService = new OrderService($orderRepository);
    }

    /**
     * @covers ::__construct
     * @return void
     */
    public function test__construct()
    {
        $this->createOrderService();
        $this->assertInstanceOf(OrderService::class, $this->orderService);
    }

    /**
     * @covers ::getOrders
     * @covers ::getOrder
     * @return void
     */
    public function testGetOrderAndOrders()
    {
        $singleOrderId = 'mockOrder1';
        $orderIds = ['mockOrder1', 'mockOrder2'];
        $context = $this->createMock(Context::class);

        $orderCollection = $this->createMock(OrderCollection::class);
        $orderCollection->expects($this->exactly(1))
            ->method('first')
            ->willReturn($this->createMock(OrderEntity::class));

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->expects($this->exactly(3))
            ->method('getEntities')
            ->willReturnOnConsecutiveCalls($orderCollection, $orderCollection, $this->createMock(OrderCollection::class));

        $orderRepository = $this->createMock(EntityRepositoryInterface::class);
        $orderRepository->expects($this->exactly(3))
            ->method('search')
            ->withConsecutive(
                [
                    $this->callback($this->getCriteriaCallback($orderIds)),
                    $this->equalTo($context)
                ],
                [
                    $this->callback($this->getCriteriaCallback([$singleOrderId])),
                    $this->equalTo($context)
                ]
            )
            ->willReturn($searchResult);


        $this->createOrderService($orderRepository);

        $result = $this->orderService->getOrders($orderIds, $context);

        $this->assertEquals($orderCollection, $result);

        try {
            $result = $this->orderService->getOrder($singleOrderId, $context);
            $this->assertInstanceOf(OrderEntity::class, $result);
        } catch (\Throwable $throwable) {
            $this->fail();
        }

        try {
            $this->orderService->getOrder($singleOrderId, $context);
        } catch (\Throwable $throwable) {
            $this->assertEquals(
                'Could not find order with id ' . $singleOrderId,
                $throwable->getMessage()
            );
        }


    }


    /**
     * @covers ::updateOrderCustomFields
     * @return void
     */
    public function testUpdateOrderCustomFields()
    {
        $singleOrderId = 'mockOrder1';
        $customFields = ['mockCustomKey' => 'mockCustomValue'];
        $orderRepository = $this->createMock(EntityRepositoryInterface::class);
        $context = $this->createMock(Context::class);
        $order = $this->createMock(OrderEntity::class);

        $order->expects($this->once())
            ->method('getCustomFields')
            ->willReturn([Defaults::CUSTOM_FIELDS_KEY => []]);
        $order->expects($this->once())
            ->method('getId')
            ->willReturn($singleOrderId);

        $orderCollection = $this->createMock(OrderCollection::class);
        $orderCollection->expects($this->once())
            ->method('first')
            ->willReturn($order);

        $searchResult = $this->createMock(EntitySearchResult::class);
        $searchResult->expects($this->once())
            ->method('getEntities')
            ->willReturn($orderCollection);

        $orderRepository->expects($this->once())
            ->method('search')
            ->withConsecutive(
                [
                    $this->callback($this->getCriteriaCallback([$singleOrderId])),
                    $this->equalTo($context)
                ]
            )
            ->willReturn($searchResult);

        $orderRepository->expects($this->once())
            ->method('update')
            ->with($this->equalTo(
                [
                    [
                        'id'=>$singleOrderId,
                        'customFields'=>[
                            Defaults::CUSTOM_FIELDS_KEY => $customFields
                        ]
                    ]
                ]
            ));

        $this->createOrderService($orderRepository);

        $this->orderService->updateOrderCustomFields($singleOrderId, $customFields, $context);
    }

    /**
     * @param array $orderIds
     * @return Closure
     */
    private function getCriteriaCallback(array $orderIds): Closure
    {
        return function ($criteria) use ($orderIds) {
            /** @var Criteria $criteria */
            $sameIds = $criteria->getIds() == $orderIds;

            $hasAssociations = true;
            foreach (['currency', 'deliveries', 'deliveries',
                         'documents', 'lineItems', 'salesChannel'] as $associationKey) {
                // Fix: hasAssociation only looks at top level
                $hasAssociations &= $criteria->hasAssociation($associationKey);
            }

            return $sameIds && boolval($hasAssociations);//The IDE lies here
        };
    }

    private function createSearchableOrderRepoMock(int $count, array $consecutive, $searchResult)
    {
        $orderRepository = $this->createMock(EntityRepositoryInterface::class);
        $orderRepository->expects($this->exactly($count))
            ->method('search')
            ->withConsecutive($consecutive)
            ->willReturn($searchResult);
    }
}
