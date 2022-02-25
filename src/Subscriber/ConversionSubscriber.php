<?php

namespace PostNL\Shipments\Subscriber;

use PostNL\Shipments\Defaults;
use PostNL\Shipments\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shipments\Struct\ShippingMethodStruct;
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
            CartConvertedEvent::class => 'onCartConverted',
        ];
    }

    protected $attributeFactory;

    public function __construct(AttributeFactory $attributeFactory)
    {
        $this->attributeFactory = $attributeFactory;
    }

    public function onCartConverted(CartConvertedEvent $event)
    {
        $cart = $event->getCart();

        if(!$cart->hasExtensionOfType('postnl-data', ArrayStruct::class)) {
            return;
        }

        try {
            /** @var ShippingMethodStruct $attributes */
            $attributes = $this->attributeFactory->createFromEntity($cart->getDeliveries()->first()->getShippingMethod(), $event->getContext());
        } catch (\Throwable $e) {
            return;
        }

        if(is_null($attributes->getDeliveryType())) {
            return;
        }

        /** @var ArrayStruct $data */
        $data = $cart->getExtensionOfType('postnl-data', ArrayStruct::class);

        $convertedCart = $event->getConvertedCart();
        $convertedCart['customFields'][Defaults::CUSTOM_FIELDS_KEY] = $data->all();

        $event->setConvertedCart($convertedCart);
    }
}
