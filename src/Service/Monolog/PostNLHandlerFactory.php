<?php

namespace PostNL\Shipments\Service\Monolog;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Shopware\Core\Framework\Log\Monolog\DoctrineSQLHandler;
use Shopware\Core\Kernel;
use Symfony\Component\HttpFoundation\Session\Session;

class PostNLHandlerFactory
{
    private $configService;

    /**
     * @param Session $session
     */
    public function __construct(
        // ConfigService
    )
    {
    }

    /**
     * @param $filename
     * @param $retentionDays
     * @return RotatingFileHandler
     */
    public function createFileHandler($filename, $retentionDays): RotatingFileHandler
    {
        return new RotatingFileHandler($filename, $retentionDays, $this->getMinimumMonologLevel());
    }

    /**
     * @return DoctrineSQLHandler
     */
    public function createSQLHandler(): DoctrineSQLHandler
    {
        return new DoctrineSQLHandler(Kernel::getConnection(), $this->getMinimumMonologLevel());
    }

    /**
     * @return int
     */
    private function getMinimumMonologLevel(): int
    {
//        if ($this->configService->isDebugMode()) {
//            return Logger::DEBUG;
//        }

        return Logger::INFO;
    }

}
