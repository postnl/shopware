<?php

namespace PostNL\Shopware6\Service\Shopware\ShippingMethod;

use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService;
use Shopware\Core\Checkout\Payment\Cart\Error\PaymentMethodBlockedError;
use Shopware\Core\Checkout\Shipping\Cart\Error\ShippingMethodBlockedError;
use Shopware\Core\Checkout\Shipping\SalesChannel\AbstractShippingMethodRoute;
use Shopware\Core\Checkout\Shipping\SalesChannel\ShippingMethodRouteResponse;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextPersister;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextService;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class ShippingMethodRouteDecorator extends AbstractShippingMethodRoute
{

    private AbstractShippingMethodRoute $decoratedService;
    private ConfigService $configService;
    private SalesChannelContextPersister $salesChannelContextPersister;
    private CartService $cartService;

    /**
     * @param AbstractShippingMethodRoute  $decoratedService
     * @param ConfigService                $configService
     * @param SalesChannelContextPersister $channelContextPersister
     * @param CartService                  $cartService
     */
    public function __construct(AbstractShippingMethodRoute  $decoratedService,
                                ConfigService                $configService,
                                SalesChannelContextPersister $channelContextPersister,
                                CartService                  $cartService)
    {
        $this->decoratedService = $decoratedService;
        $this->configService = $configService;
        $this->salesChannelContextPersister = $channelContextPersister;
        $this->cartService = $cartService;
    }

    public function getDecorated(): AbstractShippingMethodRoute
    {
        return $this->decoratedService;
    }

    public function load(Request $request, SalesChannelContext $context, Criteria $criteria): ShippingMethodRouteResponse
    {
        $originalResult = $this->decoratedService->load($request, $context, $criteria);
//return $originalResult;
        $config = $this->configService->getConfiguration($context->getSalesChannelId(), $context->getContext());

        $shippingZone = ZoneService::getDestinationZone(
            $config->getSenderAddress()->getCountrycode(),
            $context->getShippingLocation()->getCountry()->getIso()
        );

        /**
         * @var string               $key
         * @var ShippingMethodEntity $shippingMethod
         */
        foreach ($originalResult->getShippingMethods() as $key => $shippingMethod) {
            //Get custom field postnl
            if (isset($shippingMethod->getCustomFields()['postnl']['deliveryType'])) {
                if (!$config->isSendToEU() && $shippingZone == Zone::EU) {
                    $originalResult->getShippingMethods()->remove($key);
                    continue;
                }
                if (!$config->isSendToWorld() && $shippingZone == Zone::GLOBAL) {
                    $originalResult->getShippingMethods()->remove($key);
                    continue;
                }
                if (!in_array($shippingZone, [Zone::NL, Zone::BE]) &&
                    $shippingMethod->getCustomFields()['postnl']['deliveryType'] == DeliveryType::PICKUP) {
                    $originalResult->getShippingMethods()->remove($key);
                    continue;
                }
                if ($shippingZone != Zone::NL &&
                    $shippingMethod->getCustomFields()['postnl']['deliveryType'] == DeliveryType::MAILBOX) {
                    $originalResult->getShippingMethods()->remove($key);
                }
            }
        }

        if (!in_array($context->getShippingMethod()->getId(), $originalResult->getShippingMethods()->getIds())) {
            $cart = $this->cartService->getCart($context->getToken(), $context);

            $cart->addErrors(
                new ShippingMethodBlockedError((string) $context->getShippingMethod()->getTranslation('name'))
            );

            $this->cartService->recalculate($cart, $context);
        }

        return $originalResult;
    }
}
