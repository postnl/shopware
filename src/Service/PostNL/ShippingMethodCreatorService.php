<?php

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Service\Shopware\ShippingMethodService;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ShippingMethodCreatorService
{
    private MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function create(ActivateContext $activateContext, ContainerInterface $container, string $path): array
    {
        /** @var ShippingMethodService $shippingMethodService */
        $shippingMethodService = new ShippingMethodService(
            $container->get('delivery_time.repository'),
            $container->get('media.repository'),
            $container->get('rule.repository'),
            $container->get('shipping_method.repository'),
            $this->mediaService,
            $container->get('postnl.logger')
        );
        return $shippingMethodService->createShippingMethods($path, $activateContext->getContext());
    }
}
