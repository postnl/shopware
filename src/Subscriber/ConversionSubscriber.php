<?php

namespace PostNL\Shopware6\Subscriber;

use DateTimeInterface;
use Firstred\PostNL\Entity\Request\GetLocation;
use Firstred\PostNL\Entity\Request\GetSentDate;
use Firstred\PostNL\Entity\Response\ResponseLocation;
use Firstred\PostNL\Exception\InvalidArgumentException;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\CustomFieldHelper;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\PostNL\Product\DefaultProductService;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\CountryService;
use PostNL\Shopware6\Service\Shopware\DataExtractor\ShippingMethodDataExtractor;
use PostNL\Shopware6\Service\Shopware\DeliveryDateService;
use PostNL\Shopware6\Struct\Attribute\ProductAttributeStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Checkout\Cart\Order\OrderConvertedEvent;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConversionSubscriber implements EventSubscriberInterface
{
    protected ApiFactory                  $apiFactory;
    protected AttributeFactory            $attributeFactory;
    protected ConfigService               $configService;
    protected CountryService              $countryService;
    protected EntityRepository            $productRepository;
    protected DeliveryDateService         $deliveryDateService;
    protected DefaultProductService       $defaultProductService;
    protected ShippingMethodDataExtractor $shippingMethodDataExtractor;
    protected LoggerInterface             $logger;

    public function __construct(
        ApiFactory                  $apiFactory,
        AttributeFactory            $attributeFactory,
        ConfigService               $configService,
        CountryService              $countryService,
        EntityRepository            $productRepository,
        DeliveryDateService         $deliveryDateService,
        DefaultProductService       $defaultProductService,
        ShippingMethodDataExtractor $shippingMethodDataExtractor,
        LoggerInterface             $logger
    )
    {
        $this->apiFactory = $apiFactory;
        $this->attributeFactory = $attributeFactory;
        $this->configService = $configService;
        $this->countryService = $countryService;
        $this->productRepository = $productRepository;
        $this->deliveryDateService = $deliveryDateService;
        $this->defaultProductService = $defaultProductService;
        $this->shippingMethodDataExtractor = $shippingMethodDataExtractor;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CartConvertedEvent::class  => [
                ['addSendDate', 600],
                ['addPostNLProductId', 500],
                ['addShopwareProductData', 400],
                ['addTypeCodeToAddresses', 200],
                ['addDeliveryTypeData', 100],
                ['addPickupPointAddress', 100],
                ['storeCartData', 100],
                ['restorePostNLData', 100],
            ],
            OrderConvertedEvent::class => [
                ['storePostNLData', 100],
            ],
        ];
    }

    /**
     * If an order is converted to a cart, which happens when changing something on an order in the admin, then we have
     * to store the order custom fields as an extension on the cart, or else those will be removed.
     * @param OrderConvertedEvent $event
     * @return void
     */
    public function storePostNLData(OrderConvertedEvent $event)
    {
        $order = $event->getOrder();
        $data = $order->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY] ?? [];

        $cart = $event->getConvertedCart();
        $cart->addExtension(CartService::ORIGINAL_DATA, new ArrayStruct($data));

        $event->setConvertedCart($cart);
    }

    /**
     * When a cart is converted to an order, and it has the extension set by storePostNLData, then we merge that data
     * into the custom fields of the new order.
     * @param CartConvertedEvent $event
     * @return void
     */
    public function restorePostNLData(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if (!$cart->hasExtensionOfType(CartService::ORIGINAL_DATA, ArrayStruct::class)) {
            return;
        }

        $data = $cart->getExtensionOfType(CartService::ORIGINAL_DATA, ArrayStruct::class);

        if ($data->count() === 0) {
            return;
        }

        $convertedCart = $event->getConvertedCart();
        CustomFieldHelper::merge($convertedCart, $data->all());
        $event->setConvertedCart($convertedCart);
    }

    public function storeCartData(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if ($cart->hasExtensionOfType(CartService::ORIGINAL_DATA, ArrayStruct::class)) {
            return;
        }

        if (!$cart->hasExtensionOfType(CartService::EXTENSION, ArrayStruct::class)) {
            return;
        }

        $data = $cart->getExtensionOfType(CartService::EXTENSION, ArrayStruct::class);

        if ($data->count() === 0) {
            return;
        }

        $convertedCart = $event->getConvertedCart();
        CustomFieldHelper::merge($convertedCart, $data->all());
        $event->setConvertedCart($convertedCart);
    }

    /**
     * @throws \Exception
     */
    public function addSendDate(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if ($cart->hasExtensionOfType(CartService::ORIGINAL_DATA, ArrayStruct::class)) {
            return;
        }

        if (!$cart->hasExtensionOfType(CartService::EXTENSION, ArrayStruct::class)) {
            return;
        }

        $deliveryType = $this->shippingMethodDataExtractor->extractDeliveryType($cart->getDeliveries()->first()->getShippingMethod());

        if (empty($deliveryType)) {
            return;
        }

        if ($deliveryType !== DeliveryType::SHIPMENT) {
            return;
        }

        $deliveryAddress = $cart->getDeliveries()->first()->getLocation()->getAddress();

        $context = $event->getSalesChannelContext();
        $config = $this->configService->getConfiguration($context->getSalesChannelId(), $context->getContext());

        $city = $deliveryAddress->getCity();
        $countryCode = $deliveryAddress->getCountry()->getIso();

        $customFields = $deliveryAddress->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY] ?? [];
        $houseNumber = $customFields[Defaults::CUSTOM_FIELDS_HOUSENUMBER_KEY] ?? null;
        $houseNumberExt = $customFields[Defaults::CUSTOM_FIELDS_HOUSENUMBER_ADDITION_KEY] ?? null;
        $street = $customFields[Defaults::CUSTOM_FIELDS_STREETNAME_KEY] ?? null;

        $deliveryOptions = $config->getDeliveryOptions();
        $postalCode = $deliveryAddress->getZipcode();
        $cartExtension = $cart->getExtension(CartService::EXTENSION);

        /** @var DateTimeInterface $deliveryDate */
        $deliveryDate = $cartExtension[Defaults::CUSTOM_FIELDS_DELIVERY_DATE_KEY];

        $shippingDuration = $config->getShippingDuration();

        try {
            $getSentDate = new GetSentDate(
                null,
                $city,
                $countryCode,
                $houseNumber,
                $houseNumberExt,
                $deliveryOptions,
                $postalCode,
                $deliveryDate,
                $street,
                $shippingDuration,
            );
        }
        catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return;
        }
        $context = $event->getSalesChannelContext();

        //Get data
        try {
            $sentDateResponse = $this->deliveryDateService->getSentDate($context, $getSentDate);
        }
        catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return;
        }

        $sentDateTime = $sentDateResponse->getSentDate();

        if (!$sentDateTime instanceof DateTimeInterface) {
            //$this->logger->error('Sent date time is not a DateTimeInterface', ['SentDateTime' => $sentDateTime]);
            return;
        }

        try {
            $cutOffTimeParts = explode(':', $config->getCutOffTime());
        }
        catch (\Throwable $e) {
            $cutOffTimeParts = [0, 0];
        }

        $sentDateTime = \DateTime::createFromFormat(DATE_ATOM, $sentDateTime->format(DATE_ATOM));
        $sentDateTime->setTime(...$cutOffTimeParts);

        $convertedCart = $event->getConvertedCart();

        CustomFieldHelper::merge(
            $convertedCart,
            [
                Defaults::CUSTOM_FIELDS_SENT_DATE_KEY => $sentDateTime->format(DATE_ATOM),
            ]
        );

        $event->setConvertedCart($convertedCart);
    }

    public function addShopwareProductData(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if ($cart->hasExtensionOfType(CartService::ORIGINAL_DATA, ArrayStruct::class)) {
            return;
        }

        $convertedCart = $event->getConvertedCart();

        /** @var array $lineItem */
        foreach ($convertedCart['lineItems'] as $key => $lineItem) {
            if (empty($lineItem['type'])) {
                continue;
            }

            if ($lineItem['type'] !== LineItem::PRODUCT_LINE_ITEM_TYPE) {
                continue;
            }

            if (empty($lineItem['payload'])) {
                continue;
            }

            $productCriteria = new Criteria([$lineItem['referencedId']]);

            /** @var ProductEntity $product */
            $product = $this->productRepository->search($productCriteria, $event->getContext())->first();

            /** @var ProductAttributeStruct $productAttributes */
            $productAttributes = $this->attributeFactory->create(
                ProductAttributeStruct::class,
                $product->getTranslation('customFields'),
                $event->getContext()
            );

            if (!$product instanceof ProductEntity) {
                continue;
            }

            if (empty($lineItem['payload'][Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY])) {
                $convertedCart['lineItems'][$key]['payload'][Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY]
                    = $product->getWeight();
            }

            if (empty($lineItem['payload'][Defaults::LINEITEM_PAYLOAD_TARIFF_KEY])) {
                $convertedCart['lineItems'][$key]['payload'][Defaults::LINEITEM_PAYLOAD_TARIFF_KEY]
                    = $productAttributes->getPostnlProductHsCode();
            }

            if (
                empty($lineItem['payload'][Defaults::LINEITEM_PAYLOAD_ORIGIN_KEY])
                && !empty($productAttributes->getPostnlProductCountryOfOrigin())
            ) {
                $convertedCart['lineItems'][$key]['payload'][Defaults::LINEITEM_PAYLOAD_ORIGIN_KEY]
                    = $productAttributes->getPostnlProductCountryOfOrigin()->getIso();
            }
        }

        $event->setConvertedCart($convertedCart);
    }

    public function addPostNLProductId(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if ($cart->hasExtensionOfType(CartService::ORIGINAL_DATA, ArrayStruct::class)) {
            return;
        }

        if($cart->getDeliveries()->count() === 0) {
            return;
        }

        $deliveryType = $this->shippingMethodDataExtractor->extractDeliveryType($cart->getDeliveries()->first()->getShippingMethod());

        if (empty($deliveryType)) {
            return;
        }

        $productId = $this->getPostNLProductId(
            $cart,
            $deliveryType,
            $event->getSalesChannelContext()->getSalesChannelId(),
            $event->getContext()
        );

        $convertedCart = $event->getConvertedCart();
        CustomFieldHelper::merge($convertedCart, ['productId' => $productId]);

        $event->setConvertedCart($convertedCart);
    }

    protected function getPostNLProductId(
        Cart    $cart,
        string  $deliveryType,
        string  $salesChannelId,
        Context $context
    ): string
    {
        $config = $this->configService->getConfiguration($salesChannelId, $context);

        $sourceZone = $config->getSenderAddress()->getCountrycode();
        $destinationZone = ZoneService::getDestinationZone(
            $sourceZone,
            $cart->getDeliveries()->first()->getLocation()->getCountry()->getIso()
        );

        try {
            $alternative = $this->defaultProductService->getConfigValue(
                $sourceZone,
                $destinationZone,
                $deliveryType,
                true,
                $context,
                $salesChannelId
            );

            if (
                $alternative->isEnabled() && !empty(
                    $alternative->getId() &&
                    $cart->getPrice()->getTotalPrice() >= $alternative->getCartAmount()
                )
            ) {
                return $alternative->getId();
            }
        }
        catch (\Exception $e) {
            // There probably isn't an alternative available, so only log as a debug message.
            $this->logger->debug($e->getMessage());
        }

        try {
            $default = $this->defaultProductService->getConfigValue(
                $sourceZone,
                $destinationZone,
                $deliveryType,
                false,
                $context,
                $salesChannelId
            );

            if (!empty($default->getId())) {
                return $default->getId();
            }
        }
        catch (\Exception $e) {
            // There isn't a default config available, which is possible, so only log as a debug message.
            $this->logger->debug($e->getMessage());
        }

        // At this point there is no default nor an available alternative. Use the fallback ID.
        return $this->defaultProductService->getFallback($sourceZone, $destinationZone, $deliveryType);
    }

    public function addDeliveryTypeData(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if ($cart->hasExtensionOfType(CartService::ORIGINAL_DATA, ArrayStruct::class)) {
            return;
        }

        if (!$cart->hasExtensionOfType(CartService::EXTENSION, ArrayStruct::class)) {
            return;
        }

        if($cart->getDeliveries()->count() === 0) {
            return;
        }

        $deliveryType = $this->shippingMethodDataExtractor->extractDeliveryType($cart->getDeliveries()->first()->getShippingMethod());

        if (empty($deliveryType)) {
            return;
        }

        if ($deliveryType !== DeliveryType::SHIPMENT) {
            return;
        }

        /** @var ArrayStruct $data */
        $data = $cart->getExtensionOfType(CartService::EXTENSION, ArrayStruct::class);

        $convertedCart = $event->getConvertedCart();
        CustomFieldHelper::merge(
            $convertedCart,
            [
                Defaults::CUSTOM_FIELDS_DELIVERY_DATE_KEY => $data->get(Defaults::CUSTOM_FIELDS_DELIVERY_DATE_KEY)->format(DATE_ATOM),
            ]
        );

        $event->setConvertedCart($convertedCart);
    }

    public function addPickupPointAddress(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if ($cart->hasExtensionOfType(CartService::ORIGINAL_DATA, ArrayStruct::class)) {
            return;
        }

        if($cart->getDeliveries()->count() === 0) {
            return;
        }

        $deliveryType = $this->shippingMethodDataExtractor->extractDeliveryType($cart->getDeliveries()->first()->getShippingMethod());

        if (empty($deliveryType)) {
            return;
        }

        if ($deliveryType !== DeliveryType::PICKUP) {
            return;
        }

        /** @var ArrayStruct $cartData */
        $cartData = $cart->getExtensionOfType(CartService::EXTENSION, ArrayStruct::class);

        if (!$cartData->has('pickupPointLocationCode')) {
            return;
        }

        $locationCode = $cartData->get('pickupPointLocationCode');

        $convertedCart = $event->getConvertedCart();
        CustomFieldHelper::merge($convertedCart, ['pickupPointLocationCode' => $locationCode]);
        $convertedCart = $this->setAddresses($convertedCart);

        $pickupPoint = $this->getPickupPoint($locationCode, $event->getSalesChannelContext());
        if($pickupPoint->getAddress()->getHouseNrExt() === 'PBA') {
            $pickupPoint->getAddress()->setHouseNrExt('');
            $pickupPoint->setName("Pakket- en briefautomaat");
        }

        $convertedCart = $this->setPickupPointAsDeliveryAddresses($convertedCart, $pickupPoint, $event->getContext());

        $event->setConvertedCart($convertedCart);
    }

    public function addTypeCodeToAddresses(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if ($cart->hasExtensionOfType(CartService::ORIGINAL_DATA, ArrayStruct::class)) {
            return;
        }

        $convertedCart = $event->getConvertedCart();

        foreach ($convertedCart['deliveries'] as $i => $delivery) {
            CustomFieldHelper::merge(
                $convertedCart['deliveries'][$i]['shippingOrderAddress'],
                [
                    'addressType' => '01',
                ]
            );
        }

        if (array_key_exists('addresses', $convertedCart)) {
            foreach ($convertedCart['addresses'] as $i => $existingAddress) {
                CustomFieldHelper::merge(
                    $convertedCart['addresses'][$i],
                    [
                        'addressType' => '01',
                    ]
                );
            }
        }

        $event->setConvertedCart($convertedCart);
    }

    protected function setAddresses(array $convertedCart): array
    {
        $addresses = array_column($convertedCart['deliveries'], 'shippingOrderAddress');

        if (array_key_exists('addresses', $convertedCart)) {
            foreach ($convertedCart['addresses'] as $existingAddress) {
                $addresses[] = $existingAddress;
            }
        }

        foreach ($convertedCart['deliveries'] as &$delivery) {
            if (!array_key_exists('shippingOrderAddress', $delivery)) {
                continue;
            }

            $delivery['shippingOrderAddressId'] = $delivery['shippingOrderAddress']['id'];
            unset($delivery['shippingOrderAddress']);
        }

        $convertedCart['addresses'] = $addresses;

        return $convertedCart;
    }

    protected function getPickupPoint(
        int                 $locationCode,
        SalesChannelContext $context
    ): ResponseLocation
    {
        $apiClient = $this->apiFactory->createClientForSalesChannel($context->getSalesChannelId(), $context->getContext());

        $locationResult = $apiClient->getLocation(new GetLocation($locationCode));
        return $locationResult->getGetLocationsResult()->getResponseLocation()[0];
    }

    protected function setPickupPointAsDeliveryAddresses(
        array            $convertedCart,
        ResponseLocation $pickupPoint,
        Context          $context
    )
    {
        foreach ($convertedCart['deliveries'] as &$delivery) {
            $deliveryAddressId = $delivery['shippingOrderAddressId'];
            $deliveryAddress = array_filter($convertedCart['addresses'], fn (array $address) => $address['id'] === $deliveryAddressId)[0];

            $pickupPointAddress = [
                'id'           => Uuid::randomHex(),
                'salutationId' => $deliveryAddress['salutationId'],
                'firstName'    => $deliveryAddress['firstName'],
                'lastName'     => $deliveryAddress['lastName'],
                'company'      => $pickupPoint->getName(),
                'street'       => $pickupPoint->getAddress()->getStreetHouseNrExt() ??
                    sprintf(
                        '%s %s%s',
                        $pickupPoint->getAddress()->getStreet(),
                        $pickupPoint->getAddress()->getHouseNr(),
                        $pickupPoint->getAddress()->getHouseNrExt()
                    ),
                'zipcode'      => $pickupPoint->getAddress()->getZipcode(),
                'city'         => $pickupPoint->getAddress()->getCity(),
                'countryId'    => $this->countryService->getCountryByIso($pickupPoint->getAddress()->getCountrycode(), $context)->getId(),
                'customFields' => [
                    Defaults::CUSTOM_FIELDS_KEY => [
                        'addressType'               => '09',
                        'originalDeliveryAddressId' => $deliveryAddressId,
                    ],
                ],
            ];

            $convertedCart['addresses'][] = $pickupPointAddress;
            $delivery['shippingOrderAddressId'] = $pickupPointAddress['id'];
        }

        return $convertedCart;
    }
}
