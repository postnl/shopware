<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Service\Shopware\PluginService;
use Shopware\Core\Framework\Context;

class VersionProvider
{
    protected string        $shopwareVersion;
    protected string        $shopwareRootPath;
    protected PluginService $pluginService;

    private array $composerPackages = [];

    public function __construct(
        string        $shopwareVersion,
        string        $shopwareRootPath,
        PluginService $pluginService,
    )
    {
        $this->shopwareVersion = $shopwareVersion;
        $this->shopwareRootPath = $shopwareRootPath;
        $this->pluginService = $pluginService;
    }

    public function getAllAsString(Context $context): string
    {
        $versions = [];

        foreach ($this->getAll($context) as $key => $version) {
            $versions[] = sprintf('%s/%s', $key, $version);
        }

        return implode(' ', $versions);
    }

    public function getAll(Context $context): array
    {
        return array_filter(
            [
                'Shopware'                            => $this->getShopwareVersion(),
                $this->pluginService->getPluginName() => $this->getPluginVersion($context),
                'SDK'                                 => $this->getSDKVersion($context),
                'PHP'                                 => $this->getPHPVersion(),
            ]
        );
    }

    public function getShopwareVersion(): string
    {
        return $this->shopwareVersion;
    }

    public function getPluginVersion(Context $context): string
    {
        return $this->pluginService->getVersion($context);
    }

    public function getSDKVersion(Context $context): string
    {
        if (empty($this->composerPackages)) {
            $path = $this->pluginService->getPath($context);
            $fullPath = sprintf(
                '%s/%s/%s',
                rtrim($this->shopwareRootPath, '/'),
                rtrim($path, '/'),
                'vendor/composer/installed.php'
            );

            if (!file_exists($fullPath)) {
                return '';
            }

            try {
                $this->composerPackages = include $fullPath;
            }
            catch (\Throwable $exception) {
                return '';
            }
        }

        if (!isset($this->composerPackages['versions']['firstred/postnl-api-php']['pretty_version'])) {
            return '';
        };
        return $this->composerPackages['versions']['firstred/postnl-api-php']['pretty_version'];
    }

    public function getPHPVersion(): string
    {
        return phpversion();
    }
}