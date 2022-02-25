<?php

namespace PostNL\Shipments\Subscriber;

use Firstred\PostNL\Entity\Location;
use Firstred\PostNL\Entity\Request\GetNearestLocations;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use PostNL\Shipments\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shipments\Service\PostNL\Factory\ApiFactory;
use PostNL\Shipments\Struct\ShippingMethodStruct;
use Shopware\Core\Framework\Struct\ArrayStruct;
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
            dd($e);
            return;
        }

        switch ($attributes->getDeliveryType()) {
            case 'shipment':
                $this->handleShipment($event);
                break;
            case 'pickup':
                $this->handlePickup($event);
                break;
            case 'mailbox':
                $this->handleMailbox($event);
                break;
        }
    }

    protected function handleShipment(CheckoutConfirmPageLoadedEvent $event): void
    {
    }

    protected function handlePickup(CheckoutConfirmPageLoadedEvent $event): void
    {
        try {
            $apiClient = $this->apiFactory->createClientForSalesChannel(
                $event->getSalesChannelContext()->getSalesChannelId(),
                $event->getContext()
            );
        } catch (\Throwable $e) {
//            dd($e);
            return;
        }

        $address = $event->getPage()->getCart()->getDeliveries()->first()->getLocation()->getAddress();

        try {
            $locationRequest = new GetNearestLocations($address->getCountry()->getIso(), new Location($address->getZipcode()));
            $locationResponse = $apiClient->getNearestLocations($locationRequest);
        } catch(InvalidConfigurationException $e) {
            return;
        }

        $struct = new ArrayStruct();
        foreach($locationResponse->getGetLocationsResult()->getResponseLocation() as $responseLocation) {
            $struct->set($responseLocation->getId(), $responseLocation);
        }

        $event->getSalesChannelContext()->getShippingMethod()->addExtension('postnl_pickup', $struct);
    }

    protected function handleMailbox(CheckoutConfirmPageLoadedEvent $event): void
    {
    }


}
