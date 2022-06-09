<?php declare(strict_types=1);

namespace PostNL\Shopware6;

use PostNL\Shopware6\Service\PostNL\RuleCreatorService;
use PostNL\Shopware6\Service\PostNL\ShippingMethodCreatorService;
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
        parent::activate($activateContext);

        /** @var ShippingMethodCreatorService $shippingMethodCreator */
        $shippingMethodCreator = $this->container->get(ShippingMethodCreatorService::class);
        $shippingMethodCreator->create($activateContext,$this->container, $this->getPath());

        /** @var RuleCreatorService $ruleCreatorService */
        $ruleCreatorService = $this->container->get(RuleCreatorService::class);
        $ruleCreatorService->create($activateContext,$this->container);


    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);
        if ($uninstallContext->keepUserData()) {
            return;
        }

        //TODO Figure out better lifecycle
        $uninstallContext->getMigrationCollection()->migrateDestructiveInPlace();
    }
}

