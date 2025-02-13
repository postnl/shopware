<?php declare(strict_types=1);

namespace PostNL\Shopware6;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Service\PostNL\RuleCreatorService;
use PostNL\Shopware6\Service\PostNL\ShippingRulePriceCreatorService;
use PostNL\Shopware6\Service\Shopware\CustomField\CustomFieldInstaller;
use PostNL\Shopware6\Service\Shopware\ShippingMethodService;
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
        if(version_compare($updateContext->getCurrentPluginVersion(), "4", ">=") &&
            version_compare($updateContext->getCurrentPluginVersion(), "4.1.0", "<")) {
            // If current version of the plugin is higher than v4, abut lower than 4.1.0
            $shippingMethodService = ShippingMethodService::instance($this->container);

            $mediaId = $shippingMethodService->getMediaId($this->getPath(), $updateContext->getContext());
            $shippingMethodService->saveIcon(
                $this->getPath(),
                ShippingMethodService::ICON_NAME,
                $updateContext->getContext(),
                $mediaId
            );
        }

        CustomFieldInstaller::createFactory($this->container)->install($updateContext->getContext());
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        $shippingMethodService = ShippingMethodService::instance($this->container);
        $shippingMethodIDs = $shippingMethodService->createShippingMethods($this->getPath(), $activateContext->getContext());

        /** @var RuleCreatorService $ruleCreatorService */
        $ruleCreatorService = $this->container->get(RuleCreatorService::class);
        $ruleIDs = $ruleCreatorService->create($activateContext, $this->container);

        /** @var ShippingRulePriceCreatorService $shippingRulePriceCreator */
        $shippingRulePriceCreator = $this->container->get(ShippingRulePriceCreatorService::class);
        $shippingRulePriceCreator->create(
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

        try {
            CustomFieldInstaller::createFactory($this->container)->uninstall($uninstallContext->getContext());
        } catch(\Throwable $e) {
        }

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);

        $connection->executeStatement("DROP TABLE `postnl_option_requirement_mapping`");
        $connection->executeStatement("DROP TABLE `postnl_product_option_optional_mapping`");
        $connection->executeStatement("DROP TABLE `postnl_product_option_required_mapping`");
        $connection->executeStatement("DROP TABLE `postnl_option_translation`");
        $connection->executeStatement("DROP TABLE `postnl_product_translation`");
        $connection->executeStatement("DROP TABLE `postnl_option`");
        $connection->executeStatement("DROP TABLE `postnl_product`");
    }
}

