<?php

namespace PostNL\Shopware6\Subscriber;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Struct\Attribute\ProductAttributeStruct;
use PostNL\Shopware6\Struct\Attribute\ShippingMethodAttributeStruct;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
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

    public function __construct(
        AttributeFactory          $attributeFactory,
        ConfigService             $configService,
        EntityRepositoryInterface $productRepository
    )
    {
        $this->attributeFactory = $attributeFactory;
        $this->configService = $configService;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            CartConvertedEvent::class => [
                ['addPostNLProductId', 500],
                ['addShopwareProductData', 400],
                ['addDeliveryTypeData', 100],
            ],
        ];
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

        switch($sourceZone) {
            case Zone::NL:
                switch($destinationZone) {
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
                switch($destinationZone) {
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
