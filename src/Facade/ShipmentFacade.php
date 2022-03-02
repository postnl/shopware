<?php

namespace PostNL\Shipments\Facade;

use PostNL\Shipments\Defaults;
use PostNL\Shipments\Service\PostNL\ShipmentService;
use PostNL\Shipments\Service\Shopware\OrderService;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

class ShipmentFacade
{
    protected $orderService;
    protected $shipmentService;

    public function __construct(
        OrderService    $orderService,
        ShipmentService $shipmentService
    )
    {
        $this->orderService = $orderService;
        $this->shipmentService = $shipmentService;
    }

    public function generateBarcodes(array $orderIds, Context $context): array
    {
        $orders = $this->orderService->getOrders($orderIds, $context);

        $ordersWithoutBarcode = $orders->filter(function (OrderEntity $order) {
            return !array_key_exists('barCode', $order->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY]);
        });

        return $this->shipmentService->generateBarcodesForOrders($ordersWithoutBarcode, $context);
    }

    public function shipOrders(
        array   $orderIds,
        bool    $overrideProduct,
        string  $overrideProductId,
        Context $context
    ): string
    {
        if ($overrideProduct) {
            foreach ($orderIds as $orderId) {
                $this->orderService->updateOrderCustomFields($orderId, ['productId' => $overrideProductId], $context);
            }
        }

        $orders = $this->orderService->getOrders($orderIds, $context);

        $pdf = $this->shipmentService->shipOrders($orders, $context);

        return $pdf;
    }
}
