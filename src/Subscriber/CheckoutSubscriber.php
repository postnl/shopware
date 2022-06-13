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
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Psr\Log\LoggerInterface;
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
     * @var CartService
     */
    protected $cartService;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param ApiFactory       $apiFactory
     * @param AttributeFactory $attributeFactory
     * @param CartService      $cartService
     * @param LoggerInterface  $logger
     */
    public function __construct(
        ApiFactory $apiFactory,
        AttributeFactory $attributeFactory,
        CartService $cartService,
        LoggerInterface $logger
    )
    {
        $this->apiFactory = $apiFactory;
        $this->attributeFactory = $attributeFactory;
        $this->cartService = $cartService;
        $this->logger = $logger;
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
            // This is not a PostNL shipping method
            return;
        }

        $this->logger->debug('Handling checkout data for PostNL shipping method', [
            'shippingMethod' => $event->getSalesChannelContext()->getShippingMethod()
        ]);

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
        $this->logger->debug('Handling checkout data for pickup point shipping method');

        try {
            $apiClient = $this->apiFactory->createClientForSalesChannel(
                $event->getSalesChannelContext()->getSalesChannelId(),
                $event->getContext()
            );
        } catch (\Throwable $e) {
            $this->logger->error('Could not get an API client for saleschannel', [
                'salesChannelId' => $event->getSalesChannelContext()->getSalesChannelId(),
                'exception' => $e,
            ]);
            return;
        }

        $address = $event->getPage()->getCart()->getDeliveries()->first()->getLocation()->getAddress();

        $this->logger->debug('Getting nearest pickup points for address', [
            'address' => $address,
        ]);

        try {
            $locationRequest = new GetNearestLocations($address->getCountry()->getIso(), new Location($address->getZipcode()));
            $locationResponse = $apiClient->getNearestLocations($locationRequest);
        } catch (PostNLException $e) {
            $this->logger->error('Could not fetch nearest pickup points', [
                'exception' => $e,
                'address' => $address
            ]);
            return;
        } catch (\Throwable $e) {
            $this->logger->error('Could not fetch nearest pickup points', [
                'exception' => $e,
                'address' => $address
            ]);
            return;
        }

        $locationsResult = $locationResponse->getGetLocationsResult();

        if (!$locationsResult instanceof GetLocationsResult) {
            $this->logger->error('Get Nearest Locations: API returned an unexpected result', [
                'result' => $locationsResult
            ]);
            return;
        }

        $this->logger->debug('Fetched nearest pickup points for address', [
            'address' => $address,
            'result' => $locationsResult,
        ]);

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

        $this->logger->debug('Setting closest pickup points', [
            'pickupPoints' => $pickupPoints,
        ]);

        $event->getSalesChannelContext()->getShippingMethod()->addExtension('postnl_pickup', $pickupPoints);
    }

    protected function handleMailbox(CheckoutConfirmPageLoadedEvent $event): void
    {
    }

}
