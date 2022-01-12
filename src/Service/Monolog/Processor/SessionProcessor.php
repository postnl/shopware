<?php

namespace PostNL\Shipments\Service\Monolog\Processor;

use Monolog\Processor\ProcessorInterface;

class SessionProcessor implements ProcessorInterface
{
    /**
     * @var string|null
     */
    private $sessionId;

    public function __construct(string $sessionId = null)
    {
        $this->sessionId = $sessionId;
    }

    public function __invoke(array $record)
    {
        if(empty($this->sessionId)) {
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
