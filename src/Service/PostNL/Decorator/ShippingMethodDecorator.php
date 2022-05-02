<?php

namespace PostNL\Shopware6\Service\PostNL\Decorator;

use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneMapping;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\ZoneService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use Shopware\Core\Checkout\Shipping\SalesChannel\AbstractShippingMethodRoute;
use Shopware\Core\Checkout\Shipping\SalesChannel\ShippingMethodRouteResponse;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\Request;

class ShippingMethodDecorator extends AbstractShippingMethodRoute
{

    private AbstractShippingMethodRoute $decoratedService;
    private ConfigService $configService;

    /**
     * @param AbstractShippingMethodRoute $decoratedService
     * @param ConfigService $configService
     */
    public function __construct(AbstractShippingMethodRoute $decoratedService, ConfigService $configService)
    {
        $this->decoratedService = $decoratedService;
        $this->configService = $configService;
    }


    public function getDecorated(): AbstractShippingMethodRoute
    {
        return $this->decoratedService;
    }

    public function load(Request $request, SalesChannelContext $context, Criteria $criteria): ShippingMethodRouteResponse
    {
        $originalResult = $this->decoratedService->load($request, $context, $criteria);

        $config = $this->configService->getConfiguration($context->getSalesChannelId(), $context->getContext());

        $shippingZone = ZoneService::getDestinationZone(
            $config->getSenderAddress()->getCountrycode(),
            $context->getShippingLocation()->getCountry()->getIso()
        );

        /**
         * @var string $key
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

        dump($context,$context->getShippingMethod());
        return $originalResult;
    }

    private function removeShippingFromContext(SalesChannelContext $context,ShippingMethodEntity $entity)
    {
        if ($context->getShippingMethod()->getId()==$entity->getId()){

        }
    }
}
