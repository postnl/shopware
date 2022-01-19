<?php

namespace PostNL\Shipments\Service\Shopware;

use PostNL\Shipments\Service\Attribute\AttributeFactory;
use PostNL\Shipments\Struct\Config\ConfigStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ConfigService
{
    const DOMAIN = 'PostNLShipments.config.';

    /**
     * @var SystemConfigService
     */
    protected $configService;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        SystemConfigService $configService,
        AttributeFactory    $attributeFactory,
        LoggerInterface     $logger
    )
    {
        $this->configService = $configService;
        $this->attributeFactory = $attributeFactory;
        $this->logger = $logger;
    }

    public function getConfiguration(?string $salesChannelId = null)
    {
        $this->logger->debug("Getting plugin configuration", [
            'salesChannelId' => $salesChannelId,
        ]);

        try {
            $config = $this->configService->getDomain(self::DOMAIN, $salesChannelId, true);

            $data = [];
            foreach ($config as $key => $value) {
                if (str_starts_with($key, self::DOMAIN)) {
                    $key = substr($key, strlen(self::DOMAIN));
                }

                $data[$key] = $value;
            }

            return $this->attributeFactory->create(ConfigStruct::class, $data);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'salesChannelId' => $salesChannelId,
                'config' => $config ?? null,
            ]);
            throw $e;
        }
    }
}
