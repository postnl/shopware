<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\Shopware;

use PostNL\Shopware6\PostNLShopware;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Plugin\PluginEntity;
use Shopware\Core\Framework\Plugin\PluginService as ShopwarePluginService;

class PluginService
{
    protected ShopwarePluginService $pluginService;

    /**
     * @param ShopwarePluginService $pluginService
     */
    public function __construct(ShopwarePluginService $pluginService)
    {
        $this->pluginService = $pluginService;
    }

    /**
     * @param Context $context
     * @return PluginEntity
     */
    public function getPlugin(Context $context): PluginEntity
    {
        return $this->pluginService->getPluginByName($this->getPluginName(), $context);
    }

    /**
     * @param Context $context
     * @return string
     */
    public function getAuthor(Context $context): string
    {
        return $this->getPlugin($context)->getAuthor() ?? '';
    }

    /**
     * @param Context $context
     * @return string
     */
    public function getVersion(Context $context): string
    {
        return $this->getPlugin($context)->getVersion();
    }

    /**
     * @param Context $context
     * @return string
     */
    public function getUpgradeVersion(Context $context): string
    {
        return $this->getPlugin($context)->getUpgradeVersion() ?? '';
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        $className = PostNLShopware::class;
        $chunks = explode('\\', $className);
        return end($chunks);
    }
}
