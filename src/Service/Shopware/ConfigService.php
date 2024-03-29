<?php declare(strict_types=1);

namespace PostNL\Shopware6\Service\Shopware;

use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\ShopwareHttpException;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ConfigService
{
    const DOMAIN = 'PostNLShopware.config.';

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

    /**
     * @param string|null $salesChannelId
     * @param Context     $context
     * @return ConfigStruct
     */
    public function getConfiguration(?string $salesChannelId, Context $context): ConfigStruct
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

            /** @var ConfigStruct $struct */
            $struct = $this->attributeFactory->create(ConfigStruct::class, $data, $context);

            return $struct;
        } catch (ShopwareHttpException $e) {
            $this->logger->critical($e->getMessage(), array_merge([
                'salesChannelId' => $salesChannelId,
                'config' => $config ?? null,
            ], $e->getParameters()));

            throw $e;
        }
    }
}
