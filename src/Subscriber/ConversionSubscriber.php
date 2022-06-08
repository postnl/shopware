<?php

namespace PostNL\Shopware6\Subscriber;

use PostNL\Shopware6\Defaults;

use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Struct\Attribute\ShippingMethodAttributeStruct;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConversionSubscriber implements EventSubscriberInterface
{
    protected $attributeFactory;

    protected $configService;

    /**
     * @var EntityRepository
     */
    protected $productRepository;

    /**
     * @param $attributeFactory
     * @param $configService
     * @param $productRepository
     */
    public function __construct($attributeFactory, $configService, $productRepository)
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
                ['addProductData', 500],
                ['addWeight', 400],
                ['addDeliveryTypeData', 100],
            ],
        ];
    }

    public function addWeight(CartConvertedEvent $event)
    {
        //Get the cart
        $convertedCart = $event->getConvertedCart();
        //Get each of the line items and add the product weight to it in the payload
        /** @var array $lineItem */
        foreach ($convertedCart['lineItems'] as $key => $lineItem) {
            //If product type
            if (empty($lineItem['type'])) {
                continue;
            }
            if ($lineItem['type'] !== LineItem::PRODUCT_LINE_ITEM_TYPE) {
                continue;
            }
            if (empty($lineItem['payload'])) {
                continue;
            }
            if (!empty($lineItem['payload'][Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY])) {
                continue;
            }
            //Get product weight if not already in there
            $productCriteria = new Criteria([$lineItem['referencedId']]);
            $repositoryProduct = $this->productRepository->search($productCriteria, $event->getContext())->first();

            if (!$repositoryProduct instanceof ProductEntity) {
                continue;
            }
            // Add the weight to the original product/payload
            $weight = $repositoryProduct->getWeight();
            //Add it back
            $convertedCart['lineItems'][$key]['payload'][Defaults::LINEITEM_PAYLOAD_WEIGHT_KEY] = $weight;
        }
        //Don't forget to add it back to the event.
        $event->setConvertedCart($convertedCart);
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
