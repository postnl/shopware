<?php

namespace PostNL\Shopware6\Subscriber;

use DateTimeInterface;
use Firstred\PostNL\Entity\Request\GetLocation;
use Firstred\PostNL\Entity\Request\GetSentDate;
use Firstred\PostNL\Entity\Response\ResponseLocation;
use Firstred\PostNL\Exception\InvalidArgumentException;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\PostNL\Product\DefaultProductService;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\CountryService;
use PostNL\Shopware6\Service\Shopware\DeliveryDateService;
use PostNL\Shopware6\Struct\Attribute\ProductAttributeStruct;
use PostNL\Shopware6\Struct\Attribute\ShippingMethodAttributeStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
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
     * @var CountryService
     */
    protected $countryService;

    /**
     * @var EntityRepository
     */
    protected $productRepository;

    /**
     * @var DeliveryDateService
     */
    protected $deliveryDateService;

    /**
     * @var DefaultProductService
     */
    protected $defaultProductService;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    public function __construct(
        ApiFactory            $apiFactory,
        AttributeFactory      $attributeFactory,
        ConfigService         $configService,
        CountryService        $countryService,
        EntityRepository      $productRepository,
        DeliveryDateService   $deliveryDateService,
        DefaultProductService $defaultProductService,
        LoggerInterface       $logger
    )
    {
        $this->apiFactory = $apiFactory;
        $this->attributeFactory = $attributeFactory;
        $this->configService = $configService;
        $this->countryService = $countryService;
        $this->productRepository = $productRepository;
        $this->deliveryDateService = $deliveryDateService;
        $this->defaultProductService = $defaultProductService;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CartConvertedEvent::class => [
                ['addPostNLProductId', 500],
                ['addShopwareProductData', 400],
                ['addSendDate', 600],
                ['addDeliveryTypeData', 100],
                ['addPickupPointAddress', 100],
            ],
        ];
    }

    /**
     * @throws \Exception
     */
    public function addSendDate(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if (!$cart->hasExtensionOfType(CartService::EXTENSION, ArrayStruct::class)) {
            return;
        }

        $deliveryAdress = $cart->getDeliveries()->first()->getLocation()->getAddress();

        $context = $event->getSalesChannelContext();
        $config = $this->configService->getConfiguration($context->getSalesChannelId(), $context->getContext());

        $allowSundaySorting = true;//TODO: from config?
        $city = $deliveryAdress->getCity();
        $countryCode = $deliveryAdress->getCountry()->getIso();

        $customFields = $deliveryAdress->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY];
        $houseNumber = $customFields[Defaults::CUSTOM_FIELDS_HOUSENUMBER_KEY];
        $houseNumberExt = $customFields[Defaults::CUSTOM_FIELDS_HOUSENUMBER_ADDITION_KEY];
        $street = $customFields[Defaults::CUSTOM_FIELDS_STREETNAME_KEY];

        $deliveryOptions = $config->getDeliveryOptions();
        $postalCode = $deliveryAdress->getZipcode();
        $cartExtension = $cart->getExtension(CartService::EXTENSION);
        $deliveryDate = $cartExtension[Defaults::CUSTOM_FIELDS_DELIVERY_DATE_KEY];

        $shippingDuration = $config->getShippingDuration();

        try {
            $getSentDate = new GetSentDate(
                $allowSundaySorting,
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
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return;
        }
        $context = $event->getSalesChannelContext();

        //Get data
        try {
            $sentDateResponse = $this->deliveryDateService->getSentDate($context, $getSentDate);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return;
        }


        $sentDateTime = $sentDateResponse->getSentDate();

        if (!$sentDateTime instanceof DateTimeInterface) {
            $this->logger->error('Sent date time is not a DateTimeInterface', ['SentDateTime' => $sentDateTime]);
            return;
        }

        $sentDateTime = new \DateTime(
            $sentDateTime->format(\Shopware\Core\Defaults::STORAGE_DATE_TIME_FORMAT),
            new \DateTimeZone('UTC')
        );

        $convertedCart = $event->getConvertedCart();
        $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] = array_merge_recursive(
            $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] ?? [],
            [
                Defaults::CUSTOM_FIELDS_SENT_DATE_KEY => date_format($sentDateTime, DATE_ATOM),
            ]
        );

        $event->setConvertedCart($convertedCart);
    }

    public function addShopwareProductData(CartConvertedEvent $event)
    {
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

            if (empty($lineItem['payload'][Defaults::LINEITEM_PAYLOAD_ORIGIN_KEY])
                && !empty($productAttributes->getPostnlProductCountryOfOrigin())) {
                $convertedCart['lineItems'][$key]['payload'][Defaults::LINEITEM_PAYLOAD_ORIGIN_KEY]
                    = $productAttributes->getPostnlProductCountryOfOrigin()->getIso();
            }
        }

        $event->setConvertedCart($convertedCart);
    }

    public function addPostNLProductId(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        try {
            /** @var ShippingMethodAttributeStruct $attributes */
            $attributes = $this->attributeFactory->createFromEntity($cart->getDeliveries()->first()->getShippingMethod(), $event->getContext());
        } catch (\Throwable $e) {
            return;
        }

        if (is_null($attributes->getDeliveryType())) {
            return;
        }

        $productId = $this->getPostNLProductId(
            $cart,
            $attributes,
            $event->getSalesChannelContext()->getSalesChannelId(),
            $event->getContext()
        );

        $convertedCart = $event->getConvertedCart();
        $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] = array_merge(
            $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] ?? [],
            [
                'productId' => $productId,
            ]
        );

        $event->setConvertedCart($convertedCart);
    }

    protected function getPostNLProductId(
        Cart                          $cart,
        ShippingMethodAttributeStruct $shippingMethodAttributes,
        string                        $salesChannelId,
        Context                       $context
    ): string
    {
        $config = $this->configService->getConfiguration($salesChannelId, $context);

        $sourceZone = $config->getSenderAddress()->getCountrycode();
        $destinationZone = ZoneService::getDestinationZone(
            $sourceZone,
            $cart->getDeliveries()->first()->getLocation()->getCountry()->getIso()
        );
        $deliveryType = $shippingMethodAttributes->getDeliveryType();

        try {
            $alternative = $this->defaultProductService->getConfigValue(
                $sourceZone,
                $destinationZone,
                $deliveryType,
                true,
                $context,
                $salesChannelId
            );

            if ($alternative->isEnabled() && $cart->getPrice()->getTotalPrice() >= $alternative->getCartAmount()) {
                return $alternative->getId();
            }
        } catch (\Exception $e) {
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

            return $default->getId();
        } catch (\Exception $e) {
            // There isn't a default config available, which is possible, so only log as a debug message.
            $this->logger->debug($e->getMessage());
        }

        // At this point there is no default nor an available alternative. Use the fallback ID.
        return $this->defaultProductService->getFallback($sourceZone, $destinationZone, $deliveryType);
    }

    public function addDeliveryTypeData(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if (!$cart->hasExtensionOfType(CartService::EXTENSION, ArrayStruct::class)) {
            return;
        }

        try {
            /** @var ShippingMethodAttributeStruct $attributes */
            $attributes = $this->attributeFactory->createFromEntity($cart->getDeliveries()->first()->getShippingMethod(), $event->getContext());
        } catch (\Throwable $e) {
            return;
        }

        if (is_null($attributes->getDeliveryType())) {
            return;
        }

        /** @var ArrayStruct $data */
        $data = $cart->getExtensionOfType(CartService::EXTENSION, ArrayStruct::class);

        $convertedCart = $event->getConvertedCart();
        $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] = array_merge_recursive(
            $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] ?? [],
            $data->all()
        );

        $event->setConvertedCart($convertedCart);
    }

    public function addPickupPointAddress(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        try {
            /** @var ShippingMethodAttributeStruct $attributes */
            $attributes = $this->attributeFactory->createFromEntity($cart->getDeliveries()->first()->getShippingMethod(), $event->getContext());
        } catch (\Throwable $e) {
            return;
        }

        if (is_null($attributes->getDeliveryType())) {
            return;
        }

        /** @var ArrayStruct $cartData */
        $cartData = $cart->getExtensionOfType(CartService::EXTENSION, ArrayStruct::class);

        $convertedCart = $event->getConvertedCart();
        $convertedCart = $this->setAddresses($convertedCart);


        if ($attributes->getDeliveryType() === DeliveryType::PICKUP && $cartData->has('pickupPointLocationCode')) {
            $pickupPoint = $this->getPickupPoint(
                $cartData->get('pickupPointLocationCode'),
                $event->getSalesChannelContext()
            );

            $convertedCart = $this->setPickupPointAsDeliveryAddresses($convertedCart, $pickupPoint, $event->getContext());
        }

        $event->setConvertedCart($convertedCart);
    }

    protected function setAddresses(array $convertedCart): array
    {
        $shippingAddresses = array_column($convertedCart['deliveries'], 'shippingOrderAddress');

        $addresses = array_map(function (array $shippingAddress) {
            $shippingAddress['customFields'] = array_merge_recursive($shippingAddress['customFields'] ?? [], [
                Defaults::CUSTOM_FIELDS_KEY => [
                    'addressType' => '01',
                ],
            ]);

            return $shippingAddress;
        }, $shippingAddresses);

        if (array_key_exists('addresses', $convertedCart)) {
            foreach ($convertedCart['addresses'] as $existingAddress) {
                $addresses[] = $existingAddress;
            }
        }

        foreach ($convertedCart['deliveries'] as &$delivery) {
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
            $deliveryAddress = array_filter($convertedCart['addresses'], function (array $address) use ($deliveryAddressId) {
                return $address['id'] === $deliveryAddressId;
            })[0];

            $pickupPointAddress = [
                'id' => Uuid::randomHex(),
                'salutationId' => $deliveryAddress['salutationId'],
                'firstName' => $deliveryAddress['firstName'],
                'lastName' => $deliveryAddress['lastName'],
                'company' => $pickupPoint->getName(),
                'street' => $pickupPoint->getAddress()->getStreetHouseNrExt() ??
                    sprintf(
                        '%s %s%s',
                        $pickupPoint->getAddress()->getStreet(),
                        $pickupPoint->getAddress()->getHouseNr(),
                        $pickupPoint->getAddress()->getHouseNrExt()
                    ),
                'zipcode' => $pickupPoint->getAddress()->getZipcode(),
                'city' => $pickupPoint->getAddress()->getCity(),
                'countryId' => $this->countryService->getCountryByIso($pickupPoint->getAddress()->getCountrycode(), $context)->getId(),
                'customFields' => [
                    Defaults::CUSTOM_FIELDS_KEY => [
                        'addressType' => '09',
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
