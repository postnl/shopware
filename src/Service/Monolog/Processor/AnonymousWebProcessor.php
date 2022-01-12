<?php

namespace PostNL\Shipments\Service\Monolog\Processor;

use Monolog\Processor\WebProcessor;
use PostNL\Shipments\Service\Monolog\Anonymizer\AnonymizerInterface;

class AnonymousWebProcessor extends WebProcessor
{
    /**
     * @var AnonymizerInterface[]
     */
    private $anonymizers;

    /**
     * @param array $anonymizers
     */
    public function __construct(array $anonymizers = [])
    {
        parent::__construct();

        $this->anonymizers = $anonymizers;
    }

    /**
     * @param array $record
     * @return array
     */
    public function __invoke(array $record): array
    {
        $record = parent::__invoke($record);

        foreach ($this->anonymizers as $anonymizer) {
            $record = $anonymizer->anonymize($record);
        }

        return $record;
    }

}
