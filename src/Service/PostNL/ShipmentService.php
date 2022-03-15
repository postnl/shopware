<?php

namespace PostNL\Shopware6\Service\PostNL;

use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Amount;
use Firstred\PostNL\Entity\Dimension;
use Firstred\PostNL\Entity\Label;
use Firstred\PostNL\Entity\Request\GetLocation;
use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Entity\Shipment;
use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\PostNL\Product\ProductService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shopware6\Service\Shopware\OrderService;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

class ShipmentService
{
    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    /**
     * @var OrderDataExtractor
     */
    protected $orderDataExtractor;

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var LabelService
     */
    protected $labelService;

    /**
     * @var ProductService
     */
    protected $productService;


    public function __construct(
        ApiFactory         $apiFactory,
        OrderDataExtractor $orderDataExtractor,
        OrderService       $orderService,
        ConfigService      $configService,
        LabelService       $labelService,
        ProductService     $productService
    )
    {
        $this->apiFactory = $apiFactory;
        $this->orderDataExtractor = $orderDataExtractor;
        $this->orderService = $orderService;
        $this->configService = $configService;
        $this->labelService = $labelService;
        $this->productService = $productService;
    }

    /**
     * @param OrderCollection $orders
     * @param Context $context
     * @return array<string, string>
     * @throws PostNLException
     * @throws \Firstred\PostNL\Exception\CifDownException
     * @throws \Firstred\PostNL\Exception\CifException
     * @throws \Firstred\PostNL\Exception\HttpClientException
     * @throws \Firstred\PostNL\Exception\InvalidBarcodeException
     * @throws \Firstred\PostNL\Exception\InvalidConfigurationException
     * @throws \Firstred\PostNL\Exception\ResponseException
     */
    public function generateBarcodesForOrders(OrderCollection $orders, Context $context): array
    {
        $barCodesAssigned = [];

        // Yes, this should be getSalesChannelIds.
        foreach (array_unique(array_values($orders->getSalesChannelIs())) as $salesChannelId) {
            $apiClient = $this->apiFactory->createClientForSalesChannel($salesChannelId, $context);

            $salesChannelOrders = $orders->filterBySalesChannelId($salesChannelId);

            $isoCodes = $salesChannelOrders->map(function (OrderEntity $order) {
                return $this->orderDataExtractor->extractDeliveryCountry($order)->getIso();
            });

            try {
                $barCodes = $apiClient->generateBarcodesByCountryCodes(array_count_values($isoCodes));
            } catch (PostNLException $e) {
                // TODO log
                dd($e);
                throw $e;
            }

            foreach ($salesChannelOrders as $order) {
                $iso = $this->orderDataExtractor->extractDeliveryCountry($order)->getIso();
                $barCode = array_pop($barCodes[$iso]);

                $barCodesAssigned[$order->getId()] = $barCode;

                $this->orderService->updateOrderCustomFields($order->getId(), ['barCode' => $barCode], $context);
            }
        }

        return $barCodesAssigned;
    }

    public function shipOrders(OrderCollection $orders, bool $confirm, Context $context)
    {
        $response = [];

        $config = $this->configService->getConfiguration(null, $context);

        $format = $config->getPrinterFormat() === 'a4' ? Label::FORMAT_A4 : Label::FORMAT_A6;
        $a6Orientation = 'P';//$config->getPrinterA6Orientation();

        $printerType = 'GraphicFile|PDF';
        $confirm = $config->isAutoConfirmShipment() || $confirm;

        $positions = [
            1 => true,
            2 => true,
            3 => true,
            4 => true,
        ];

        // Yes, this should be getSalesChannelIds.
        foreach (array_unique(array_values($orders->getSalesChannelIs())) as $salesChannelId) {
            $apiClient = $this->apiFactory->createClientForSalesChannel($salesChannelId, $context);

            $salesChannelOrders = $orders->filterBySalesChannelId($salesChannelId);

            $shipments = [];
            foreach ($salesChannelOrders as $order) {
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
                $a6Orientation
            );

            foreach ($labelResponses as $labelResponse) {
                $response[] = $labelResponse;
            }

            foreach ($salesChannelOrders as $order) {
                $this->orderService->updateOrderCustomFields($order->getId(), ['confirm' => $confirm], $context);
            }
        }

        if ($printerType !== 'GraphicFile|PDF') {
            return $response;
        }

        return $this->labelService->mergeLabels(
            $response,
            $format,
            $positions,
            $a6Orientation
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
        if ($senderAddress instanceof Address) {
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

        if ($product->getDeliveryType() === DeliveryType::PICKUP) {
            $pickupPointLocationCode = $order->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY]['pickupPointLocationCode'];

            $locationResult = $apiClient->getLocation(new GetLocation($pickupPointLocationCode));
            $pickupLocation = $locationResult->getGetLocationsResult()->getResponseLocation()[0];

            $address = $pickupLocation->getAddress()->setAddressType('09');
            $address->setCompanyName($pickupLocation->getName());
            $addresses[] = $address;

            $shipment->setDeliveryAddress('09');
        }

        $addresses[] = $receiverAddress;
        $shipment->setAddresses($addresses);

        $amounts = [];

        if (!!$product->getInsurance()) {
            $insuredAmount = new Amount();
            $insuredAmount->setAmountType('02');
            $insuredAmount->setValue($order->getAmountTotal());
            $amounts[] = $insuredAmount;
        }

        $shipment->setAmounts($amounts);

        return $shipment;
    }
}
