<?php

namespace PostNL\Shipments\Facade;

use PostNL\Shipments\Defaults;
use PostNL\Shipments\Service\PostNL\ShipmentService;
use PostNL\Shipments\Service\Shopware\OrderService;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

class ShipmentFacade
{
    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var ShipmentService
     */
    protected $shipmentService;

    public function __construct(
        OrderService    $orderService,
        ShipmentService $shipmentService
    )
    {
        $this->orderService = $orderService;
        $this->shipmentService = $shipmentService;
    }

    /**
     * @param string[] $orderIds
     * @param Context $context
     * @return array<string, string>
     * @throws \Firstred\PostNL\Exception\PostNLException
     */
    public function generateBarcodes(array $orderIds, Context $context): array
    {
        $orders = $this->orderService->getOrders($orderIds, $context);

        $ordersWithoutBarcode = $orders->filter(function (OrderEntity $order) {
            $customFields = $order->getCustomFields() ?? [];
            if(!array_key_exists(Defaults::CUSTOM_FIELDS_KEY, $customFields)) {
                return false;
            }
            return !array_key_exists('barCode', $customFields[Defaults::CUSTOM_FIELDS_KEY]);
        });

        return $this->shipmentService->generateBarcodesForOrders($ordersWithoutBarcode, $context);
    }

    /**
     * @param string[] $orderIds
     * @param bool $overrideProduct
     * @param string $overrideProductId
     * @param Context $context
     * @return string
     */
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
