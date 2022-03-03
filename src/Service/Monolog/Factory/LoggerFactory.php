<?php declare(strict_types=1);

namespace PostNL\Shopware6\Service\Monolog\Factory;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use Shopware\Core\Framework\Log\Monolog\DoctrineSQLHandler;
use Shopware\Core\Kernel;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class LoggerFactory
{
    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        SystemConfigService $systemConfigService
    )
    {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @param $filename
     * @param $retentionDays
     * @return RotatingFileHandler
     */
    public function createFileHandler($filename, $retentionDays): RotatingFileHandler
    {
        return new RotatingFileHandler($filename, $retentionDays, $this->getConfigurationBasedLogLevel());
    }

    /**
     * @return DoctrineSQLHandler
     */
    public function createSQLHandler(): DoctrineSQLHandler
    {
        return new DoctrineSQLHandler(Kernel::getConnection(), Logger::INFO);
    }

    /**
     * @return int
     */
    private function getConfigurationBasedLogLevel(): int
    {
        try {
            if ($this->systemConfigService->getBool(ConfigService::DOMAIN . 'debugMode')) {
                return Logger::DEBUG;
            }
        } catch (\Exception $e) {
        }

        return Logger::INFO;
    }
}
