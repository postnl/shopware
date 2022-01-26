<?php declare(strict_types=1);

namespace PostNL\Shipments;

use PostNL\Shipments\Service\Shopware\ShippingMethodService;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class PostNLShipments extends Plugin
{
    public function activate(ActivateContext $activateContext): void
    {
        /** @var ShippingMethodService $shippingMethodService */
        $shippingMethodService = $this->container->get(ShippingMethodService::class);
        $shippingMethodService->createShippingMethod($this->getPath(), $activateContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $uninstallContext->getMigrationCollection()->migrateDestructiveInPlace();
    }
}

