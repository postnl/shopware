<?php declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL\Builder;

use Exception;
use Firstred\PostNL\Entity\Address;
use Firstred\PostNL\Entity\Amount;
use Firstred\PostNL\Entity\Contact;
use Firstred\PostNL\Entity\Content;
use Firstred\PostNL\Entity\Customs;
use Firstred\PostNL\Entity\Dimension;
use Firstred\PostNL\Entity\Request\GetLocation;
use Firstred\PostNL\Entity\Shipment;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\PostNL\Product\ProductService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderAddressDataExtractor;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shopware6\Struct\Attribute\OrderAttributeStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Document\DocumentEntity;
use Shopware\Core\Checkout\Document\DocumentGenerator\InvoiceGenerator;
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
     * @var OrderAddressDataExtractor
     */
    protected $orderAddressDataExtractor;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ApiFactory                $apiFactory,
        AttributeFactory          $attributeFactory,
        ConfigService             $configService,
        OrderDataExtractor        $orderDataExtractor,
        OrderAddressDataExtractor $orderAddressDataExtractor,
        ProductService            $productService,
        LoggerInterface           $logger
    )
    {
        $this->apiFactory = $apiFactory;
        $this->attributeFactory = $attributeFactory;
        $this->configService = $configService;
        $this->orderDataExtractor = $orderDataExtractor;
        $this->orderAddressDataExtractor = $orderAddressDataExtractor;
        $this->productService = $productService;
        $this->logger = $logger;
    }

    public function buildShipment(OrderEntity $order, Context $context): Shipment
    {
        $this->logger->debug('Building Shipment', [
            'orderId'           => $order->getId(),
            'orderNumber'       => $order->getOrderNumber(),
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
        $returnCustomerCode = $config->getReturnAddress()->getReturnCustomerCode();
        if (
            $config->isReturnLabelInTheBox()
            && !empty($returnCustomerCode)
            && $product->getDestinationZone() !== Zone::GLOBAL
        ) {
            $returnCountryCode = $config->getReturnAddress()->getCountrycode();

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

        if ($product->getDestinationZone() === Zone::GLOBAL) {
            $shipment->setCustoms($this->buildCustoms($order, $context));
        }

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
        $addresses = $this->orderDataExtractor->extractAddresses($order);
        $recipientAddress = $this->orderAddressDataExtractor->filterByAddressType($addresses, '01')->first();

        $address = new Address();
        $address->setAddressType('01');
        $address->setFirstName($recipientAddress->getFirstName());
        $address->setName($recipientAddress->getLastName());
        $address->setCompanyName($recipientAddress->getCompany());
        $address->setStreetHouseNrExt($recipientAddress->getStreet());
        $address->setZipcode($recipientAddress->getZipcode());
        $address->setCity($recipientAddress->getCity());
        $address->setCountrycode($this->orderAddressDataExtractor->extractCountry($recipientAddress)->getIso());

        return $address;
    }

    /**
     * @param OrderEntity $order
     * @param Context     $context
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
     * @param Context     $context
     * @return Dimension
     */
    public function buildDimension(OrderEntity $order, Context $context): Dimension
    {
        $totalWeight = 0;

        // Calculate weight per line item payload.

        foreach ($order->getLineItems() as $lineItem) {

            if (empty($lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY])) {
                continue;
            }
            //Convert to grams
            $weightInGrams = $this->convertToGramsInteger($lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY]);
            $totalWeight += ($weightInGrams * $lineItem->getQuantity());
        }

        return new Dimension($totalWeight);
    }

    public function buildCustoms(OrderEntity $order, Context $context): Customs
    {
        $config = $this->configService->getConfiguration($order->getSalesChannelId(), $context);

        $invoice = $order->getDocuments()->filter(function ($document) {
            /** @var DocumentEntity $document */
            return $document->getDocumentType()->getTechnicalName() === InvoiceGenerator::INVOICE;
        })->last();

        if ($invoice instanceof DocumentEntity) {
            $invoiceNumber = $invoice->getConfig()['documentNumber'];
        } else {
            $invoiceNumber = $order->getOrderNumber();
        }

        $customs = new Customs();
        $customs->setCurrency($order->getCurrency()->getIsoCode());
        $customs->setHandleAsNonDeliverable("false");
        $customs->setInvoice('true');
        $customs->setInvoiceNr($invoiceNumber);
        $customs->setShipmentType('Commercial Goods');

        $contents = [];
        $i = 0;
        foreach ($order->getLineItems() as $lineItem) {
            $weight = 0.0;

            if (!empty($lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY])) {
                $weight = $lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY] * $lineItem->getQuantity();
            }

            $tariffNr = '000000';

            if (!empty($config->getFallbackHSCode())) {
                $tariffNr = $config->getFallbackHSCode();
            }

            if (!empty($lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_TARIFF_KEY])) {
                $tariffNr = $lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_TARIFF_KEY];
            }

            $countryOfOrigin = $config->getSenderAddress()->getCountrycode();

            if (!empty($lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_ORIGIN_KEY])) {
                $countryOfOrigin = $lineItem->getPayload()[Defaults::LINEITEM_PAYLOAD_ORIGIN_KEY];
            }

            $content = new Content();
            $content->setDescription($lineItem->getLabel());
            $content->setQuantity((string)$lineItem->getQuantity());
            $content->setWeight((string)$this->convertToGramsInteger($weight));
            $content->setValue((string)$lineItem->getTotalPrice());
            $content->setHSTariffNr($tariffNr);
            $content->setCountryOfOrigin($countryOfOrigin);

            $contents[] = $content;
            if (++$i >= 5) {
                break;
            }
        }

        $customs->setContent($contents);

        return $customs;
    }

    /**
     * Converts g to kg
     *
     * @param float|int $weightInKG
     * @return int
     */
    private function convertToGramsInteger($weightInKG): int
    {
        //Shopware only allows a precision of 3 digits
        $weightInGrams = $weightInKG * 1000;
        return intval(round($weightInGrams));
    }
}
