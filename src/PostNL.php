<?php declare(strict_types=1);

namespace PostNL\Shopware6;

use PostNL\Shopware6\Service\Shopware\ShippingMethodService;
use Shopware\Core\Content\Media\MediaService;
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

class PostNL extends Plugin
{
    public function activate(ActivateContext $activateContext): void
    {
        /** @var ShippingMethodService $shippingMethodService */
        $shippingMethodService = new ShippingMethodService(
            $this->container->get('delivery_time.repository'),
            $this->container->get('media.repository'),
            $this->container->get('rule.repository'),
            $this->container->get('shipping_method.repository'),
            $this->container->get(MediaService::class),
            $this->container->get('postnl.logger')
        );
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

