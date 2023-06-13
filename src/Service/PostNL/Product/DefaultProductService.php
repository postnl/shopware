<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL\Product;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Struct\Config\ProductSelectionStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;

class DefaultProductService
{
    protected ConfigService $configService;

    protected LoggerInterface $logger;

    public function __construct(
        ConfigService   $configService,
        LoggerInterface $logger
    )
    {
        $this->configService = $configService;
        $this->logger = $logger;
    }

    public function getConfigValue(
        string  $sourceZone,
        string  $destinationZone,
        string  $deliveryType,
        bool    $alternative,
        Context $context,
        ?string $salesChannelId = null
    ): ProductSelectionStruct
    {
        $config = $this->configService->getConfiguration($salesChannelId, $context);

        $method = implode('', [
            'getProduct',
            ucfirst(strtolower($deliveryType)),
            ucfirst(strtolower($sourceZone)),
            ucfirst(strtolower($destinationZone)),
            ucfirst(strtolower($alternative ? 'alternative' : 'default')),
        ]);

        if(!method_exists($config, $method)) {
            throw new \Exception(sprintf('Method %s does not exist in config', $method));
        }

        $struct = $config->{$method}();

        if(!$struct instanceof ProductSelectionStruct) {
            throw new \Exception('Unexpected config value');
        }

        return $struct;
    }

    /**
     * @param string $sourceZone
     * @param string $destinationZone
     * @param string $deliveryType
     * @return string
     * @throws \Exception
     */
    public function getFallback(
        string $sourceZone,
        string $destinationZone,
        string $deliveryType
    )
    {
        /** @var array<string, string> $constants */
        $constants = Defaults::getConstants();

        $constant = strtoupper(sprintf(
            '%s_%s_%s_%s',
            'product',
            $deliveryType,
            $sourceZone,
            $destinationZone,
        ));

        if (array_key_exists($constant, $constants) && !empty($constants[$constant])) {
            return $constants[$constant];
        }

        throw new \Exception("No fallback product. Please check whether this combination should exist.");
    }
}
