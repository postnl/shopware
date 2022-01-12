<?php

namespace PostNL\Shipments\Service\Monolog;

use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\UidProcessor;
use PostNL\Shipments\Service\Monolog\Anonymizer\IPAnonymizer;
use PostNL\Shipments\Service\Monolog\Processor\AnonymousWebProcessor;
use PostNL\Shipments\Service\Monolog\Processor\SessionProcessor;

class PostNLLogger extends Logger
{
    const CHANNEL = 'PostNL';

    /**
     * @param array $handlers
     * @param string|null $sessionId
     */
    public function __construct(array $handlers = [], ?string $sessionId = null)
    {
        parent::__construct(
            self::CHANNEL,
            $handlers,
            [
                new UidProcessor(),
                new IntrospectionProcessor(Logger::ERROR),
                new SessionProcessor($sessionId),
                new AnonymousWebProcessor([
                    new IPAnonymizer('*')
                ]),
            ]
        );
    }
}
