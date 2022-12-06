<?php

namespace PostNL\Shopware6\Subscriber;

use DateTimeInterface;
use Firstred\PostNL\Entity\Request\GetSentDate;
use Firstred\PostNL\Exception\InvalidArgumentException;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DeliveryDateService;
use PostNL\Shopware6\Struct\Attribute\ProductAttributeStruct;
use PostNL\Shopware6\Struct\Attribute\ShippingMethodAttributeStruct;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConversionSubscriber implements EventSubscriberInterface
{
    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var EntityRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DeliveryDateService
     */
    private $deliveryDateService;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        AttributeFactory          $attributeFactory,
        ConfigService             $configService,
        EntityRepositoryInterface $productRepository,
        DeliveryDateService       $deliveryDateService,
        LoggerInterface           $logger
    )
    {
        $this->attributeFactory = $attributeFactory;
        $this->configService = $configService;
        $this->productRepository = $productRepository;
        $this->deliveryDateService = $deliveryDateService;
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

        $shippingDuration = $config->getTransitTime();

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
            dump($e);
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
        $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] = array_merge(
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

        $config = $this->configService->getConfiguration(
            $event->getSalesChannelContext()->getSalesChannelId(),
            $event->getContext()
        );

        $productId = $this->getPostNLProductId($cart, $config, $attributes);

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
        ConfigStruct                  $config,
        ShippingMethodAttributeStruct $shippingMethodAttributes
    ): string
    {
        $sourceZone = $config->getSenderAddress()->getCountrycode();
        $destinationZone = ZoneService::getDestinationZone(
            $sourceZone,
            $cart->getDeliveries()->first()->getLocation()->getCountry()->getIso()
        );
        $deliveryType = $shippingMethodAttributes->getDeliveryType();

        switch ($sourceZone) {
            case Zone::NL:
                switch ($destinationZone) {
                    case Zone::NL:
                        switch ($deliveryType) {
                            case DeliveryType::MAILBOX:
                                return Defaults::PRODUCT_MAILBOX_NL_NL;
                            case DeliveryType::SHIPMENT:
                                $default = $config->getProductShipmentNlNlDefault();
                                $alternative = $config->getProductShipmentNlNlAlternative();

                                $id = $default->getId();

                                if ($alternative->isEnabled() && $cart->getPrice()->getTotalPrice() >= $alternative->getCartAmount()) {
                                    $id = $alternative->getId();
                                }

                                if (empty($id)) {
                                    $id = Defaults::PRODUCT_SHIPPING_NL_NL;
                                }
                                return $id;
                            case DeliveryType::PICKUP:
                                $default = $config->getProductPickupNlNlDefault();

                                $id = $default->getId();

                                if (empty($id)) {
                                    $id = Defaults::PRODUCT_PICKUP_NL_NL;
                                }
                                return $id;
                        }
                        break;
                    case Zone::BE:
                        switch ($deliveryType) {
                            case DeliveryType::SHIPMENT:
                                $default = $config->getProductShipmentNlBeDefault();
                                $alternative = $config->getProductShipmentNlBeAlternative();

                                $id = $default->getId();

                                if ($alternative->isEnabled() && $cart->getPrice()->getTotalPrice() >= $alternative->getCartAmount()) {
                                    $id = $alternative->getId();
                                }

                                if (empty($id)) {
                                    $id = Defaults::PRODUCT_SHIPPING_NL_BE;
                                }
                                return $id;
                            case DeliveryType::PICKUP:
                                $default = $config->getProductPickupNlBeDefault();

                                $id = $default->getId();

                                if (empty($id)) {
                                    $id = Defaults::PRODUCT_PICKUP_NL_BE;
                                }
                                return $id;
                        }
                        break;
                    case Zone::EU:
                        return Defaults::PRODUCT_SHIPPING_NL_EU_4952;
                    case Zone::GLOBAL:
                        return Defaults::PRODUCT_SHIPPING_NL_GLOBAL_4945;
                }
                break;
            case Zone::BE:
                switch ($destinationZone) {
                    case Zone::BE:
                        switch ($deliveryType) {
                            case DeliveryType::SHIPMENT:
                                $default = $config->getProductShipmentBeBeDefault();
                                $alternative = $config->getProductShipmentBeBeAlternative();

                                $id = $default->getId();

                                if ($alternative->isEnabled() && $cart->getPrice()->getTotalPrice() >= $alternative->getCartAmount()) {
                                    $id = $alternative->getId();
                                }

                                if (empty($id)) {
                                    $id = Defaults::PRODUCT_SHIPPING_BE_BE;
                                }
                                return $id;
                            case DeliveryType::PICKUP:
                                $default = $config->getProductPickupBeBeDefault();

                                $id = $default->getId();

                                if (empty($id)) {
                                    $id = Defaults::PRODUCT_PICKUP_BE_BE;
                                }
                                return $id;
                        }
                        break;
                    case Zone::EU:
                        return Defaults::PRODUCT_SHIPPING_BE_EU_4952;
                    case Zone::GLOBAL:
                        return Defaults::PRODUCT_SHIPPING_BE_GLOBAL_4945;
                }
                break;
        }

        return '';
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
        $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] = array_merge(
            $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] ?? [],
            $data->all()
        );

        $event->setConvertedCart($convertedCart);
    }
}
