<?php declare(strict_types=1);

namespace PostNL\Shopware6;

use PostNL\Shopware6\Service\PostNL\RuleCreatorService;
use PostNL\Shopware6\Service\PostNL\ShippingMethodCreatorService;
use PostNL\Shopware6\Service\PostNL\ShippingMethodPriceCreatorService;
use PostNL\Shopware6\Service\PostNL\ShippingRulePriceCreatorService;
use PostNL\Shopware6\Service\Shopware\CustomField\CustomFieldInstaller;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    $loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
    if ($loader !== true) {
        spl_autoload_unregister([$loader, 'loadClass']);
        $loader->register(false);
    }
}

class PostNLShopware extends Plugin
{

    public function install(InstallContext $installContext): void
    {
        CustomFieldInstaller::createFactory($this->container)->install($installContext->getContext());
    }

    public function update(UpdateContext $updateContext): void
    {
        CustomFieldInstaller::createFactory($this->container)->install($updateContext->getContext());
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        /** @var ShippingMethodCreatorService $shippingMethodCreator */
        $shippingMethodCreator = $this->container->get(ShippingMethodCreatorService::class);
        $shippingMethodIDs = $shippingMethodCreator->create(
            $activateContext,
            $this->container,
            $this->getPath()
        );

        /** @var RuleCreatorService $ruleCreatorService */
        $ruleCreatorService = $this->container->get(RuleCreatorService::class);
        $ruleIDs = $ruleCreatorService->create($activateContext, $this->container);

        /** @var ShippingRulePriceCreatorService $shippingMethodPriceCreator */
        $shippingMethodPriceCreator = $this->container->get(ShippingRulePriceCreatorService::class);
        $shippingMethodPriceCreator->create(
            $shippingMethodIDs,
            $ruleIDs,
            $activateContext,
            $this->container
        );

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

