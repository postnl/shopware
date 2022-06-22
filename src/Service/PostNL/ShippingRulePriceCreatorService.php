<?php

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Service\Shopware\ShippingRulePriceService;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ShippingRulePriceCreatorService
{
    public function create(array $shippingMethodIds, array $ruleIds, ActivateContext $activateContext, ContainerInterface $container)
    {
        $ruleService = new ShippingRulePriceService(
            $container->get('shipping_method_price.repository'),
            $container->get('postnl.logger')
        );
        $ruleService->createPricingMatrices($shippingMethodIds,$ruleIds,$activateContext->getContext());
    }
}
