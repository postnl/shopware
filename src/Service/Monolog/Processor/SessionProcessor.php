<?php

namespace PostNL\Shopware6\Service\Monolog\Processor;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

class SessionProcessor implements ProcessorInterface
{
    private string $sessionId = '';

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        try {
            $this->sessionId = trim($requestStack->getSession()->getId());
        }
        catch (SessionNotFoundException $e) {
        }
    }

    /**
     * @param LogRecord $record
     * @return LogRecord
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        if (empty($this->sessionId)) {
            return $record;
        }

        $record['extra']['session'] = $this->sessionId;

        return $record;
    }
}
