<?php declare(strict_types=1);

namespace PostNl\Shipments;

use PostNl\Shipments\Service\ShippingMethod\ShippingMethodService;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class PostNlShipments extends Plugin
{
    public function activate(ActivateContext $activateContext): void
    {
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

