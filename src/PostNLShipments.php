<?php declare(strict_types=1);

namespace PostNL\Shipments;

use PostNL\Shipments\Service\Shopware\ShippingMethodService;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    $loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
    if ($loader !== true) {
        spl_autoload_unregister([$loader, 'loadClass']);
        $loader->register(false);
    }
}

class PostNLShipments extends Plugin
{
    public function activate(ActivateContext $activateContext): void
    {
        /** @var ShippingMethodService $shippingMethodService */
        $shippingMethodService = $this->container->get(ShippingMethodService::class);
        $shippingMethodService->createShippingMethods($this->getPath(), $activateContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        //TODO Figure out better lifecycle
        $uninstallContext->getMigrationCollection()->migrateDestructiveInPlace();
    }
}

