<?php

namespace PostNL\Shipments\Service\PostNL;

use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Dimension;
use Firstred\PostNL\Entity\Label;
use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Entity\Shipment;
use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shipments\Defaults;
use PostNL\Shipments\Service\PostNL\Factory\ApiFactory;
use PostNL\Shipments\Service\PostNL\Product\ProductService;
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

    protected $labelService;

    protected $productService;

    public function __construct(
        ApiFactory $apiFactory,
        OrderDataExtractor $orderDataExtractor,
        OrderService $orderService,
        LabelService $labelService,
        ProductService $productService
    )
    {
        $this->apiFactory = $apiFactory;
        $this->orderDataExtractor = $orderDataExtractor;
        $this->orderService = $orderService;
        $this->labelService = $labelService;
        $this->productService = $productService;
    }

    public function generateBarcodesForOrders(OrderCollection $orders, Context $context): array
    {
        $barCodesAssigned = [];

        // Yes, this should be getSalesChannelIds.
        foreach(array_unique(array_values($orders->getSalesChannelIs())) as $salesChannelId) {
            $apiClient = $this->apiFactory->createClientForSalesChannel($salesChannelId, $context);

            $salesChannelOrders = $orders->filterBySalesChannelId($salesChannelId);

            $isoCodes = $salesChannelOrders->map(function (OrderEntity $order) {
                return $this->orderDataExtractor->extractDeliveryCountry($order)->getIso();
            });

            try {
                $barCodes = $apiClient->generateBarcodesByCountryCodes(array_count_values($isoCodes));
            } catch(PostNLException $e) {
                dd($e);
            }

            foreach($salesChannelOrders as $order) {
                $iso = $this->orderDataExtractor->extractDeliveryCountry($order)->getIso();
                $barCode = array_pop($barCodes[$iso]);

                $barCodesAssigned[$order->getId()] = $barCode;

                $this->orderService->updateOrderCustomFields($order->getId(), ['barCode' => $barCode], $context);
            }
        }

        return $barCodesAssigned;
    }

    public function shipOrders(OrderCollection $orders, Context $context)
    {
        $response = [];

        $printerType = 'GraphicFile|PDF';
        $confirm = false;
        $format = Label::FORMAT_A4;
        $positions = [
            1 => false,
            2 => true,
            3 => false,
            4 => true,
        ];
        $a6Position = 'P';

        // Yes, this should be getSalesChannelIds.
        foreach(array_unique(array_values($orders->getSalesChannelIs())) as $salesChannelId) {
            $apiClient = $this->apiFactory->createClientForSalesChannel($salesChannelId, $context);

            $salesChannelOrders = $orders->filterBySalesChannelId($salesChannelId);

            $shipments = [];
            foreach($salesChannelOrders as $order) {
                $shipments[] = $this->createShipmentForOrder($order, $context);
            }

            /** @var GenerateLabelResponse[] $labelResponse */
            $labelResponses = $apiClient->generateLabels(
                $shipments,
                $printerType,
                $confirm,
                false,
                $format,
                $positions,
                $a6Position
            );

            foreach($labelResponses as $labelResponse) {
                $response[] = $labelResponse;
            }
        }

        if($printerType !== 'GraphicFile|PDF') {
            return $response;
        }

        return $this->labelService->mergeLabels(
            $response,
            $format,
            $positions,
            $a6Position
        );
    }

    public function createShipmentForOrder(OrderEntity $order, Context $context): Shipment
    {
        $apiClient = $this->apiFactory->createClientForSalesChannel($order->getSalesChannelId(), $context);

        $productId = $order->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY]['productId'];
        $product = $this->productService->getProduct($productId, $context);

        $shipment = new Shipment();
        $shipment->setBarcode($order->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY]['barCode']);
        $shipment->setDeliveryAddress('01');
        $shipment->setProductCodeDelivery($product->getProductCodeDelivery());
        $shipment->setReference('Order ' . $order->getOrderNumber());
        $shipment->setDimension(new Dimension(2000)); // No weight on order

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
