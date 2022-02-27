<?php

namespace PostNL\Shipments\Service\PostNL;

use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Dimension;
use Firstred\PostNL\Entity\Shipment;
use PostNL\Shipments\Defaults;
use PostNL\Shipments\Service\PostNL\Factory\ApiFactory;
use PostNL\Shipments\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shipments\Service\Shopware\OrderService;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

class ShipmentService
{
    protected $apiFactory;

    protected $orderDataExtractor;

    protected $orderService;

    public function __construct(
        ApiFactory $apiFactory,
        OrderDataExtractor $orderDataExtractor,
        OrderService $orderService
    )
    {
        $this->apiFactory = $apiFactory;
        $this->orderDataExtractor = $orderDataExtractor;
        $this->orderService = $orderService;
    }

    public function generateBarcodesForOrders(OrderCollection $orders, Context $context): void
    {
        // Yes, this should be getSalesChannelIds.
        foreach($orders->getSalesChannelIs() as $salesChannelId) {
            $apiClient = $this->apiFactory->createClientForSalesChannel($salesChannelId, $context);

            $salesChannelOrders = $orders->filterBySalesChannelId($salesChannelId);

            $isoCodes = $salesChannelOrders->map(function (OrderEntity $order) {
                return $this->orderDataExtractor->extractDeliveryCountry($order)->getIso();
            });

            $barCodes = $apiClient->generateBarcodesByCountryCodes(array_count_values($isoCodes));

            foreach($salesChannelOrders as $order) {
                $iso = $this->orderDataExtractor->extractDeliveryCountry($order)->getIso();
                $barCode = array_pop($barCodes[$iso]);

                $this->orderService->updateOrderCustomFields($order->getId(), ['barCode' => $barCode], $context);
            }
        }
    }

    public function createShipmentForOrder(OrderEntity $order, Context $context): Shipment
    {
        $apiClient = $this->apiFactory->createClientForSalesChannel($order->getSalesChannelId(), $context);

        $shipment = new Shipment();
        $shipment->setBarcode($order->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY]['barCode']);
        $shipment->setDeliveryAddress('01');
        $shipment->setProductCodeDelivery();
        $shipment->setReference('Order ' . $order->getOrderNumber());
//        $shipment->setDimension(new Dimension()) // No weight on order

        $addresses = [];

        $senderAddress = $apiClient->getCustomer()->getAddress();
        if($senderAddress instanceof Address) {
            $addresses[] = $senderAddress;
        }

        $orderAddress = $this->orderDataExtractor->extractDeliveryAddress($order);

        $receiverAddress = new Address();
        $receiverAddress->setAddressType('01');
        $receiverAddress->setFirstName($orderAddress->getFirstName());
        $receiverAddress->setName($orderAddress->getLastName());
        $receiverAddress->setCompanyName($orderAddress->getCompany());
        $receiverAddress->setStreetHouseNrExt($orderAddress->getStreet());
        $receiverAddress->setZipcode($orderAddress->getZipcode());
        $receiverAddress->setCity($orderAddress->getCity());
        $receiverAddress->setCountrycode($this->orderDataExtractor->extractDeliveryCountry($order)->getIso());

        $addresses[] = $receiverAddress;
        $shipment->setAddresses($addresses);

        // add pickup location address and set delivery address to type 09
        //$shipment->setDeliveryAddress('09');
        return $shipment;
    }
}
