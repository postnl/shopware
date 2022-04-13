<?php

namespace PostNL\Shopware6\Facade;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use PostNL\Shopware6\Service\PostNL\ShipmentService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shopware6\Service\Shopware\OrderService;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

class ShipmentFacade
{
    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var OrderDataExtractor
     */
    protected $orderDataExtractor;

    /**
     * @var ShipmentService
     */
    protected $shipmentService;

    public function __construct(
        ConfigService      $configService,
        OrderService       $orderService,
        OrderDataExtractor $orderDataExtractor,
        ShipmentService    $shipmentService
    )
    {
        $this->configService = $configService;
        $this->orderService = $orderService;
        $this->orderDataExtractor = $orderDataExtractor;
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
            if (!array_key_exists(Defaults::CUSTOM_FIELDS_KEY, $customFields)) {
                return false;
            }
            return !array_key_exists('barCode', $customFields[Defaults::CUSTOM_FIELDS_KEY]);
        });

        return $this->shipmentService->generateBarcodesForOrders($ordersWithoutBarcode, $context);
    }

    public function determineZones(array $orderIds, Context $context): array
    {
        $deliveryZones = [];

        foreach ($orderIds as $orderId) {
            $order = $this->orderService->getOrder($orderId, $context);

            $config = $this->configService->getConfiguration($order->getSalesChannelId(), $context);

            $deliveryCountry = $this->orderDataExtractor->extractDeliveryCountry($order);

            $deliveryZones[] = ZoneService::getDestinationZone(
                $config->getSenderAddress()->getCountrycode(),
                $deliveryCountry->getIso()
            );
        }

        return array_values(array_unique($deliveryZones));
    }

    /**
     * @param array $orderIds
     * @param string $productId
     * @param Context $context
     * @return void
     */
    public function changeProduct(
        array   $orderIds,
        string  $productId,
        Context $context
    ): void
    {
        foreach ($orderIds as $orderId) {
            $this->orderService->updateOrderCustomFields($orderId, ['productId' => $productId], $context);
        }
    }

    /**
     * @param string[] $orderIds
     * @param bool $confirmShipments
     * @param Context $context
     * @return string
     */
    public function shipOrders(
        array   $orderIds,
        bool    $confirmShipments,
        Context $context
    ): string
    {
        $orders = $this->orderService->getOrders($orderIds, $context);

        $pdf = $this->shipmentService->shipOrders($orders, $confirmShipments, $context);

        return $pdf;
    }
}
