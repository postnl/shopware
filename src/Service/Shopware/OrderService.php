<?php

namespace PostNL\Shopware6\Service\Shopware;

use PostNL\Shopware6\Defaults;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class OrderService
{
    /**
     * @var EntityRepository
     */
    protected $orderRepository;

    /**
     * @param EntityRepository $orderRepository
     */
    public function __construct($orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getOrders(array $orderIds, Context $context): OrderCollection
    {
        $criteria = new Criteria($orderIds);
        $criteria->addAssociation('addresses.country');
        $criteria->addAssociation('currency');
        $criteria->addAssociation('deliveries.shippingMethod');
        $criteria->addAssociation('deliveries.shippingOrderAddress.country');
        $criteria->addAssociation('documents.documentType');
        $criteria->addAssociation('lineItems');
        $criteria->addAssociation('salesChannel');

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
        $customFields = array_merge($order->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY] ?? [], $customFields);

        $this->orderRepository->update(
            [
                [
                    'id'           => $order->getId(),
                    'customFields' => [
                        Defaults::CUSTOM_FIELDS_KEY => $customFields,
                    ],
                ],
            ],
            $context
        );
    }

    public function storeTrackingCode(string $orderId, string $barcode, Context $context): void
    {
        $order = $this->getOrder($orderId, $context);
        $delivery = $order->getDeliveries()->first();

        $this->orderRepository->update(
            [
                [
                    'id'           => $orderId,
                    'deliveries' => [
                        [
                            'id' => $delivery->getId(),
                            'trackingCodes' => [$barcode],
                        ]
                    ],
                ]
            ],
            $context
        );
    }
}
