<?php declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL\Builder;

use Exception;
use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Amount;
use Firstred\PostNL\Entity\Contact;
use Firstred\PostNL\Entity\Dimension;
use Firstred\PostNL\Entity\Request\GetLocation;
use Firstred\PostNL\Entity\Shipment;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\PostNL\Product\ProductService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shopware6\Struct\Attribute\OrderAttributeStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

class ShipmentBuilder
{
    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var OrderDataExtractor
     */
    protected $orderDataExtractor;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ApiFactory         $apiFactory,
        AttributeFactory   $attributeFactory,
        ConfigService      $configService,
        OrderDataExtractor $orderDataExtractor,
        ProductService     $productService,
        LoggerInterface    $logger
    )
    {
        $this->apiFactory = $apiFactory;
        $this->attributeFactory = $attributeFactory;
        $this->configService = $configService;
        $this->orderDataExtractor = $orderDataExtractor;
        $this->productService = $productService;
        $this->logger = $logger;
    }

    public function buildShipment(OrderEntity $order, Context $context): Shipment
    {
        $this->logger->debug('Building Shipment', [
            'orderId' => $order->getId(),
            'orderNumber' => $order->getOrderNumber(),
            'orderCustomFields' => $order->getCustomFields(),
        ]);

        $apiClient = $this->apiFactory->createClientForSalesChannel($order->getSalesChannelId(), $context);

        $config = $this->configService->getConfiguration($order->getSalesChannelId(), $context);

        /** @var OrderAttributeStruct $orderAttributes */
        $orderAttributes = $this->attributeFactory->createFromEntity($order, $context);

        $product = $this->productService->getProduct($orderAttributes->getProductId(), $context);

        $addresses = [];
        $amounts = [];
        $contacts = [];

        //= General ====
        $shipment = new Shipment();
        $shipment->setBarcode($orderAttributes->getBarCode());
        $shipment->setDeliveryAddress('01');
        $shipment->setProductCodeDelivery($product->getProductCodeDelivery());
        $shipment->setReference('Order ' . $order->getOrderNumber());

        //= Return label in the box ====
        if ($config->isReturnLabelInTheBox()) {
            $returnCountryCode = $config->getReturnAddress()->getCountrycode();
            $returnCustomerCode = $config->getReturnAddress()->getReturnCustomerCode();

            //Next 2 lines are a temp fix for non usage of returncode in SDK 2/3
            $originalCustomerCode = $apiClient->getCustomer()->getCustomerCode();
            $apiClient->getCustomer()->setCustomerCode($returnCustomerCode);

            $returnBarcode = $apiClient->generateBarcode(
                '3S',
                $returnCustomerCode,
                null,
                strtoupper($returnCountryCode) === 'BE'
            );

            //Rest of the fix for the usage of returncode 3/3
            $apiClient->getCustomer()->setCustomerCode($originalCustomerCode);

            $shipment->setReturnBarcode($returnBarcode);

            $addresses[] = $this->buildReturnAddress($order, $context);
        }

        //= Mailbox ====
        if ($product->getDeliveryType() === DeliveryType::MAILBOX) {
            $shipment->setDeliveryDate(date_create()->format('d-m-Y 15:00:00'));
        }


        //= Addresses ====
        $addresses[] = $this->buildReceiverAddress($order);

        if ($product->getDeliveryType() === DeliveryType::PICKUP) {
            $addresses[] = $this->buildPickupLocationAddress($order, $context);

            $shipment->setDeliveryAddress('09');
        }

        //= Amount ====
        if (!!$product->getInsurance()) {
            $amounts[] = $this->buildInsuranceAmount($order);
        }

        //= Contacts ====
        $contacts[] = $this->buildReceiverContact($order);

        //= Dimension ====
        $shipment->setDimension($this->buildDimension($order, $context));

        $shipment->setAddresses($addresses);
        $shipment->setAmounts($amounts);
        $shipment->setContacts($contacts);

        return $shipment;
    }

    public function buildReturnAddress(OrderEntity $order, Context $context): Address
    {
        $config = $this->configService->getConfiguration($order->getSalesChannelId(), $context);

        $returnAddress = $config->getReturnAddress();

        $address = new Address();
        $address->setAddressType('08');
        $address->setCompanyName($returnAddress->getCompanyName());
        $address->setZipcode($returnAddress->getZipcode());
        $address->setCity($returnAddress->getCity());
        $address->setCountrycode($returnAddress->getCountrycode());

        switch ($returnAddress->getCountrycode()) {
            case 'NL':
                $address->setStreetHouseNrExt($returnAddress->getStreet());
                break;
            default:
                $address->setStreet($returnAddress->getStreet());
                $address->setHouseNr($returnAddress->getHouseNr());
                $address->setHouseNrExt($returnAddress->getHouseNrExt());
                break;
        }

        return $address;
    }

    /**
     * @param $order
     * @return Address
     */
    public function buildReceiverAddress($order): Address
    {
        $orderAddress = $this->orderDataExtractor->extractDeliveryAddress($order);

        $address = new Address();
        $address->setAddressType('01');
        $address->setFirstName($orderAddress->getFirstName());
        $address->setName($orderAddress->getLastName());
        $address->setCompanyName($orderAddress->getCompany());
        $address->setStreetHouseNrExt($orderAddress->getStreet());
        $address->setZipcode($orderAddress->getZipcode());
        $address->setCity($orderAddress->getCity());
        $address->setCountrycode($this->orderDataExtractor->extractDeliveryCountry($order)->getIso());

        return $address;
    }

    /**
     * @param OrderEntity $order
     * @param Context $context
     * @return Address
     */
    public function buildPickupLocationAddress(OrderEntity $order, Context $context): Address
    {
        /** @var OrderAttributeStruct $orderAttributes */
        $orderAttributes = $this->attributeFactory->createFromEntity($order, $context);

        if (!$orderAttributes->getPickupPointLocationCode()) {
            // TODO throw exception
        }

        $apiClient = $this->apiFactory->createClientForSalesChannel($order->getSalesChannelId(), $context);

        $locationResult = $apiClient->getLocation(new GetLocation($orderAttributes->getPickupPointLocationCode()));
        $pickupPoint = $locationResult->getGetLocationsResult()->getResponseLocation()[0];

        $address = $pickupPoint->getAddress()->setAddressType('09');
        $address->setCompanyName($pickupPoint->getName());

        return $address;
    }

    /**
     * @param OrderEntity $order
     * @return Amount
     */
    public function buildInsuranceAmount(OrderEntity $order): Amount
    {
        $amount = new Amount();
        $amount->setAmountType('02');
        $amount->setValue((string)$order->getAmountTotal());

        return $amount;
    }

    /**
     * @param OrderEntity $order
     * @return Contact
     * @throws Exception
     */
    public function buildReceiverContact(OrderEntity $order): Contact
    {
        $contact = new Contact();
        $contact->setContactType('01');
        $contact->setEmail($this->orderDataExtractor->extractCustomer($order)->getEmail());
        $contact->setTelNr($this->orderDataExtractor->extractDeliveryAddress($order)->getPhoneNumber());

        return $contact;
    }

    /**
     * @param OrderEntity $order
     * @param Context $context
     * @return Dimension
     */
    public function buildDimension(OrderEntity $order, Context $context): Dimension
    {
        $totalWeight = 0.0;
        // Calculate weight per line item payload.
        /** @var OrderLineItemEntity $lineItem */
        foreach ($order->getLineItems() as $lineItem) {
            if (empty($lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY])) {
                continue;
            }
            $totalWeight += ($lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY] * $lineItem->getQuantity());
        }
        return new Dimension($totalWeight);
    }
}
