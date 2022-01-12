<?php

namespace PostNL\Shipments\Service\Monolog\Anonymizer;

interface AnonymizerInterface
{
    public function anonymize(array $record): array;
}
