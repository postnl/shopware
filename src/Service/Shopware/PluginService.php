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
    protected ?PluginEntity $plugin = null;

    public function __construct(ShopwarePluginService $pluginService)
    {
        $this->pluginService = $pluginService;
    }

    public function getPlugin(Context $context): PluginEntity
    {
        if(!$this->plugin instanceof PluginEntity) {
            $this->plugin = $this->pluginService->getPluginByName($this->getPluginName(), $context);
        }

        return $this->plugin;
    }

    public function getAuthor(Context $context): string
    {
        return $this->getPlugin($context)->getAuthor() ?? '';
    }

    public function getPath(Context $context): string
    {
        return $this->getPlugin($context)->getPath() ?? '';
    }

    public function getVersion(Context $context): string
    {
        return $this->getPlugin($context)->getVersion();
    }

    public function getUpgradeVersion(Context $context): string
    {
        return $this->getPlugin($context)->getUpgradeVersion() ?? '';
    }

    public function getPluginName(): string
    {
        $className = PostNLShopware::class;
        $chunks = explode('\\', $className);
        return end($chunks);
    }
}
