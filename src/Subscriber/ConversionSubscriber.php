<?php

namespace PostNL\Shopware6\Subscriber;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Struct\Attribute\ShippingMethodAttributeStruct;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConversionSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            CartConvertedEvent::class => [
                ['addProductData', 500],
                ['addDeliveryTypeData', 100],
            ],
        ];
    }

    protected $attributeFactory;

    protected $configService;

    public function __construct(AttributeFactory $attributeFactory, ConfigService $configService)
    {
        $this->attributeFactory = $attributeFactory;
        $this->configService = $configService;
    }

    public function addProductData(CartConvertedEvent $event)
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

        $productId = $this->getProductId($cart, $config, $attributes);

        $convertedCart = $event->getConvertedCart();
        $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] = array_merge(
            $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] ?? [],
            [
                'productId' => $productId,
            ]
        );

        $event->setConvertedCart($convertedCart);
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

    protected function getProductId(
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

        switch ($destinationZone) {
            case Zone::NL:
                if ($sourceZone == Zone::NL) {
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
                }
                break;
            case Zone::BE:
                switch ($sourceZone) {
                    case Zone::NL:
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
                    case Zone::BE:
                        // Nothing available yet.
                        break;
                }
                break;
            case Zone::EU:
                return Defaults::PRODUCT_SHIPPING_EU_4952;
            case Zone::GLOBAL:
                return Defaults::PRODUCT_SHIPPING_GLOBAL_4947;
        }

        return '';
    }
}
