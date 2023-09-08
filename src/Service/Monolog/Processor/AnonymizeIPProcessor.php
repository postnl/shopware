<?php

namespace PostNL\Shopware6\Service\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\IpUtils;

class AnonymizeIPProcessor implements ProcessorInterface
{
    /**
     * Gets an anonymous version of the
     * provided IP address string.
     * The anonymous IP will end with a 0.
     *
     * @param LogRecord $record
     * @return LogRecord
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        if (!array_key_exists('ip', $record['extra'])) {
            return $record;
        }

        $record['extra']['ip'] = IpUtils::anonymize(trim($record['extra']['ip']));

        return $record;
    }
}
