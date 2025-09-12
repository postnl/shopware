<?php

namespace PostNL\Shopware6\Subscriber;

use Firstred\PostNL\Entity\Location;
use Firstred\PostNL\Entity\Request\GetNearestLocations;
use Firstred\PostNL\Entity\Response\GetLocationsResult;
use Firstred\PostNL\Entity\Response\ResponseLocation;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Facade\CheckoutFacade;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DataExtractor\ShippingMethodDataExtractor;
use PostNL\Shopware6\Struct\TimeframeCollection;
use PostNL\Shopware6\Struct\TimeframeStruct;
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

    protected $apiFactory;
    protected $cartService;
    protected $checkoutFacade;
    protected $configService;
    protected $shippingMethodDataExtractor;
    protected $logger;

    public function __construct(
        ApiFactory       $apiFactory,
        CartService      $cartService,
        CheckoutFacade   $checkoutFacade,
        ConfigService    $configService,
        ShippingMethodDataExtractor $shippingMethodDataExtractor,
        LoggerInterface  $logger
    )
    {
        $this->apiFactory = $apiFactory;
        $this->cartService = $cartService;
        $this->checkoutFacade = $checkoutFacade;
        $this->configService = $configService;
        $this->shippingMethodDataExtractor = $shippingMethodDataExtractor;
        $this->logger = $logger;
    }

    /**
     * @param CheckoutConfirmPageLoadedEvent $event
     * @return void
     */
    public function onCheckoutConfirmPageLoaded(CheckoutConfirmPageLoadedEvent $event)
    {
        $deliveryType = $this->shippingMethodDataExtractor->extractDeliveryType($event->getSalesChannelContext()->getShippingMethod());

        if(empty($deliveryType)) {
            return;
        }

        $this->logger->debug('Handling checkout data for PostNL shipping method', [
            'shippingMethod' => $event->getSalesChannelContext()->getShippingMethod()
        ]);

        switch ($deliveryType) {
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
        $address = $event->getPage()->getCart()->getDeliveries()->first()->getLocation()->getAddress();

        if (!in_array($address->getCountry()->getIso(), ['NL', 'BE'])) {
            return;
        }

        try {
            $deliveryDays = $this->checkoutFacade->getDeliveryDays($event->getSalesChannelContext(), $address);
            $timeframeCollection = TimeframeCollection::createFromTimeframes($deliveryDays);

            $config = $this->configService->getConfiguration($event->getSalesChannelContext()->getSalesChannelId(), $event->getContext());

            $timeframeCollection = $timeframeCollection->filterByDropoffDays($config)->filterByMaximumDaysShown($config);
        } catch (\Throwable $e) {
            $this->logger->error('Could not get delivery days', [
                'address' => $address,
                'exception' => $e->getMessage(),
            ]);
            return;
        }

        //is there a first one?
        $timeFrame = $timeframeCollection->first();

        if (!$timeFrame instanceof TimeframeStruct) {
            $this->logger->error('Get timeframes service: API returned an unexpected result', [
               // 'result' => $timeframeCollection
            ]);
            return;
        }

        if (!$event->getPage()->getCart()->hasExtensionOfType(CartService::EXTENSION, ArrayStruct::class)) {
            $event->getPage()->setCart($this->cartService->addData([
                Defaults::CUSTOM_FIELDS_DELIVERY_DATE_KEY => $timeFrame->getFrom(),
            ], $event->getSalesChannelContext()));
        }

        $this->logger->debug('Fetched timeframes for address', [
            'address' => $address,
            //'result' => $timeframeCollection,
        ]);

        $existingDeliveryDate = $this->cartService->getByKey('deliveryDate', $event->getSalesChannelContext());

        $availableDeliveryDates = $timeframeCollection->map(function ($timeFrame) {
            /** @var TimeframeStruct $timeFrame */
            return $timeFrame->getFrom();
        });

        if (!in_array($existingDeliveryDate, $availableDeliveryDates)) {
            $event->getPage()->setCart($this->cartService->addData([
                Defaults::CUSTOM_FIELDS_DELIVERY_DATE_KEY => $timeFrame->getFrom(),
            ], $event->getSalesChannelContext()));
        }

        $event->getSalesChannelContext()->getShippingMethod()->addExtension('postnl_shipment', $timeframeCollection);
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
        } catch (\Throwable $e) {
            $this->logger->error('Could not fetch nearest pickup points', [
                'exception' => $e->getMessage(),
                'address' => $address
            ]);
            return;
        }

        $locationsResult = $locationResponse->getGetLocationsResult();

        if (!$locationsResult instanceof GetLocationsResult) {
            $this->logger->error('Get Nearest Locations: API returned an unexpected result', [
                //'result' => $locationsResult
            ]);
            return;
        }

        $this->logger->debug('Fetched nearest pickup points for address', [
            'address' => $address,
            //'result' => $locationsResult,
        ]);

        $config = $this->configService->getConfiguration($event->getSalesChannelContext()->getSalesChannelId(), $event->getContext());

        $pickupPoints = new ArrayStruct();
        $locationCode = null;
        foreach ($locationsResult->getResponseLocation() as $i => $responseLocation) {
            if (is_null($locationCode)) {
                $locationCode = $responseLocation->getLocationCode();
            }

            if ($i >= $config->getPickupPointsNumberOfChoices()) {
                break;
            }

            $pickupPoints->set($responseLocation->getId(), $responseLocation);
        }

        if (!$event->getPage()->getCart()->hasExtensionOfType(CartService::EXTENSION, ArrayStruct::class)) {
            $event->getPage()->setCart($this->cartService->addData([
                'pickupPointLocationCode' => (int)$locationCode,
            ], $event->getSalesChannelContext()));
        }

        $existingLocationCode = $this->cartService->getByKey('pickupPointLocationCode', $event->getSalesChannelContext());

        $availableLocationCodes = array_map(function ($location) {
            /** @var ResponseLocation $location */
            return $location->getLocationCode();
        }, $pickupPoints->all());

        if (!in_array($existingLocationCode, $availableLocationCodes)) {
            $event->getPage()->setCart($this->cartService->addData([
                'pickupPointLocationCode' => (int)$locationCode,
            ], $event->getSalesChannelContext()));
        }

        $this->logger->debug('Setting closest pickup points', [
            //'pickupPoints' => $pickupPoints,
        ]);

        $event->getSalesChannelContext()->getShippingMethod()->addExtension('postnl_pickup', $pickupPoints);
    }

    protected function handleMailbox(CheckoutConfirmPageLoadedEvent $event): void
    {
    }
}
