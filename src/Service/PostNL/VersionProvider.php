<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL;

use Composer\InstalledVersions;
use PostNL\Shopware6\Service\Shopware\PluginService;
use Shopware\Core\Framework\Context;

class VersionProvider
{
    protected string        $shopwareVersion;
    protected PluginService $pluginService;

    public function __construct(
        string        $shopwareVersion,
        PluginService $pluginService,
    )
    {
        $this->shopwareVersion = $shopwareVersion;
        $this->pluginService = $pluginService;
    }

    public function getAllAsString(Context $context): string
    {
        $versions = [];

        foreach($this->getAll($context) as $key => $version) {
            $versions[] = sprintf('%s/%s', $key, $version);
        }

        return implode(' ', $versions);
    }

    public function getAll(Context $context): array
    {
        return array_filter([
            'Shopware' => $this->getShopwareVersion(),
            $this->pluginService->getPluginName() => $this->getPluginVersion($context),
            'SDK' => $this->getSDKVersion(),
            'PHP' => $this->getPHPVersion(),
        ]);
    }

    public function getShopwareVersion(): string
    {
        return $this->shopwareVersion;
    }

    public function getPluginVersion(Context $context): string
    {
        return $this->pluginService->getVersion($context);
    }

    public function getSDKVersion(): string
    {
        if(!class_exists(InstalledVersions::class)) {
            return '';
        }

        try {
            return InstalledVersions::getPrettyVersion('firstred/postnl-api-php') ?? '';
        }
        catch (\OutOfBoundsException $e) {
            return '';
        }
    }

    public function getPHPVersion(): string
    {
        return phpversion();
    }
}