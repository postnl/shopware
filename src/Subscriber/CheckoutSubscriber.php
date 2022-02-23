<?php

namespace PostNL\Shipments\Subscriber;

use PostNL\Shipments\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shipments\Service\PostNL\Factory\ApiFactory;
use PostNL\Shipments\Struct\ShippingMethodStruct;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckoutSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            CheckoutConfirmPageLoadedEvent::class => 'onCheckoutConfirmPageLoaded',
        ];
    }

    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @param AttributeFactory $attributeFactory
     */
    public function __construct(ApiFactory $apiFactory, AttributeFactory $attributeFactory)
    {
        $this->apiFactory = $apiFactory;
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @param CheckoutConfirmPageLoadedEvent $event
     * @return void
     */
    public function onCheckoutConfirmPageLoaded(CheckoutConfirmPageLoadedEvent $event)
    {
        try {
            /** @var ShippingMethodStruct $attributes */
            $attributes = $this->attributeFactory->createFromEntity($event->getSalesChannelContext()->getShippingMethod(), $event->getContext());
        } catch (\Throwable $e) {
//            dd($e);
        }

        switch ($attributes->getDeliveryType()) {
            case 'shipment':

                break;
            case 'pickup':
                $this->handlePickup($event);
                break;
            case 'mailbox':

                break;
        }
    }

    protected function handlePickup(CheckoutConfirmPageLoadedEvent $event)
    {
        $apiClient = $this->apiFactory->createClientForSalesChannel(
            $event->getSalesChannelContext()->getSalesChannelId(),
            $event->getContext()
        );

        dd($apiClient);

    }

}
