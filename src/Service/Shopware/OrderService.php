<?php

namespace PostNL\Shipments\Service\Shopware;

use PostNL\Shipments\Defaults;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class OrderService
{
    protected $orderRepository;

    public function __construct(EntityRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getOrders(array $orderIds, Context $context): OrderCollection
    {
        $criteria = new Criteria($orderIds);
        $criteria->addAssociation('salesChannel');
        $criteria->addAssociation('deliveries.shippingMethod');
        $criteria->addAssociation('deliveries.shippingOrderAddress.country');

        /** @var OrderCollection $orders */
        $orders = $this->orderRepository->search($criteria, $context)->getEntities();

        return $orders;
    }

    public function getOrder(string $orderId, Context $context): OrderEntity
    {
        $order = $this->getOrders([$orderId], $context)->first();

        if ($order instanceof OrderEntity) {
            return $order;
        }

        throw new \Exception('Could not find order with id ' . $orderId);
    }

    public function updateOrderCustomFields(string $orderId, array $customFields, Context $context): void
    {
        $order = $this->getOrder($orderId, $context);
        $customFields = array_merge($order->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY], $customFields);

        $this->orderRepository->update([
            [
                'id' => $order->getId(),
                'customFields' => [
                    Defaults::CUSTOM_FIELDS_KEY => $customFields,
                ]
            ]
        ], $context);
    }
}