<?php

namespace PostNL\Shipments\Service\Monolog\Anonymizer;

class IPAnonymizer implements AnonymizerInterface
{
    /**
     * @var string
     */
    private $placeholder;

    /**
     * @param string $placeholder
     */
    public function __construct($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * Gets an anonymous version of the
     * provided IP address string.
     * The anonymous IP will end with a 0.
     *
     * @param string $ip
     * @return string
     */
    public function anonymize(array $record): array
    {
        if (!array_key_exists('ip', $record['extra'])) {
            return $record;
        }

        $ip = trim($record['extra']['ip']);

        # return an empty string as the ip and return early
        # if the IP address is not even valid
        if (!$this->isValidIP($ip)) {
            $record['extra']['ip'] = '';
            return $record;
        }

        $ipOctets = explode('.', $ip);
        $record['extra']['ip'] = $ipOctets[0] . '.' . $ipOctets[1] . '.' . $ipOctets[2] . '.' . $this->placeholder;

        return $record;
    }

    /**
     * Gets if the provided IP is even a valid IP address.
     *
     * @param string $ip
     * @return bool
     */
    private function isValidIP($ip)
    {
        return (filter_var($ip, FILTER_VALIDATE_IP) !== false);
    }
}
