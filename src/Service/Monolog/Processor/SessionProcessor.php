<?php

namespace PostNL\Shopware6\Service\Monolog\Processor;

use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class SessionProcessor implements ProcessorInterface
{
    /**
     * @var string
     */
    private $sessionId;

    public function __construct(Session $session)
    {
        $this->sessionId = trim($session->getId());
    }

    public function __invoke(array $record)
    {
        if (empty($this->sessionId)) {
            return $record;
        }

        $sessionPart = substr($this->sessionId, 0, 4) . '...';

        $record['message'] .= ' (Session: ' . $sessionPart . ')';
        $record['extra'] = array_merge(
            $record['extra'],
            [
                'session' => $this->sessionId,
            ]
        );

        return $record;
    }
}
