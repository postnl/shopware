<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\Monolog;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use PostNL\Shipments\Service\Shopware\ConfigService;
use Shopware\Core\Framework\Log\Monolog\DoctrineSQLHandler;
use Shopware\Core\Kernel;

class LoggerFactory
{
    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * @param ConfigService $configService
     */
    public function __construct(
        ConfigService $configService
    )
    {
        $this->configService = $configService;
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
            if ($this->configService->getConfiguration()->isDebugMode()) {
                return Logger::DEBUG;
            }
        } catch (\Exception $e) {
        }

        return Logger::INFO;
    }
}
