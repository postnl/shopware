<?php

namespace PostNL\Shipments\Service\Monolog;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class PostNLLoggerFactory
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return PostNLLogger
     */
    public function createLogger(array $handlers): LoggerInterface
    {
        $sessionID = $this->session->getId() ?? null;

        return new PostNLLogger(
            $handlers,
            $sessionID
        );
    }
}
