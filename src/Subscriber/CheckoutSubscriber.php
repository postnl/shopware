<?php

namespace PostNL\Shopware6\Subscriber;

use Firstred\PostNL\Entity\Location;
use Firstred\PostNL\Entity\Request\GetNearestLocations;
use Firstred\PostNL\Entity\Response\GetLocationsResult;
use Firstred\PostNL\Entity\Response\ResponseLocation;
use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Struct\Attribute\ShippingMethodAttributeStruct;
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

    /** @var CartService */
    protected $cartService;

    /**
     * @param ApiFactory $apiFactory
     * @param AttributeFactory $attributeFactory
     * @param CartService $cartService
     */
    public function __construct(ApiFactory $apiFactory, AttributeFactory $attributeFactory, CartService $cartService)
    {
        $this->apiFactory = $apiFactory;
        $this->attributeFactory = $attributeFactory;
        $this->cartService = $cartService;
    }

    /**
     * @param CheckoutConfirmPageLoadedEvent $event
     * @return void
     */
    public function onCheckoutConfirmPageLoaded(CheckoutConfirmPageLoadedEvent $event)
    {
        try {
            /** @var ShippingMethodAttributeStruct $attributes */
            $attributes = $this->attributeFactory->createFromEntity($event->getSalesChannelContext()->getShippingMethod(), $event->getContext());
        } catch (\Throwable $e) {
//            dd($e);
            return;
        }

        switch ($attributes->getDeliveryType()) {
            case DeliveryType::SHIPMENT:
                $this->handleShipment($event);
                break;
            case DeliveryType::PICKUP:
                $this->handlePickup($event);
                break;
            case DeliveryType::MAILBOX:
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
            return;
        }

        $address = $event->getPage()->getCart()->getDeliveries()->first()->getLocation()->getAddress();

        try {
            $locationRequest = new GetNearestLocations($address->getCountry()->getIso(), new Location($address->getZipcode()));
            $locationResponse = $apiClient->getNearestLocations($locationRequest);
        } catch (PostNLException $e) {
            return;
        } catch (\Throwable $e) {
            return;
        }

        $locationsResult = $locationResponse->getGetLocationsResult();

        if (!$locationsResult instanceof GetLocationsResult) {
            return;
        }

        $pickupPoints = new ArrayStruct();
        $locationCode = null;
        foreach ($locationsResult->getResponseLocation() as $i => $responseLocation) {
            if (is_null($locationCode)) {
                $locationCode = $responseLocation->getLocationCode();
            }

            if ($i >= 5) {
                break;
            }

            $pickupPoints->set($responseLocation->getId(), $responseLocation);
        }

        if (!$event->getPage()->getCart()->hasExtensionOfType(CartService::EXTENSION, ArrayStruct::class)) {
            $event->getPage()->setCart($this->cartService->addData([
                'pickupPointLocationCode' => $locationCode,
            ], $event->getSalesChannelContext()));
        }

        $existingLocationCode = $this->cartService->getByKey('pickupPointLocationCode', $event->getSalesChannelContext());

        $availableLocationCodes = array_map(function ($location) {
            /** @var ResponseLocation $location */
            return $location->getLocationCode();
        }, $pickupPoints->all());

        if (!in_array($existingLocationCode, $availableLocationCodes)) {
            $event->getPage()->setCart($this->cartService->addData([
                'pickupPointLocationCode' => $locationCode,
            ], $event->getSalesChannelContext()));
        }

        $event->getSalesChannelContext()->getShippingMethod()->addExtension('postnl_pickup', $pickupPoints);
    }

    protected function handleMailbox(CheckoutConfirmPageLoadedEvent $event): void
    {
    }


}
