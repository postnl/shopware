<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\PostNL\Delivery\Zone;

class ZoneService
{
    public static function getDestinationZone($sourceCountryIso, $destinationCountryIso): string
    {
        switch($sourceCountryIso) {
            case 'NL':
                $sourceMapping = ZoneMapping::SOURCE_NL;
                break;
            case 'BE':
                $sourceMapping = ZoneMapping::SOURCE_BE;
                break;
            default:
                throw new \Exception('Shipping not supported for this source country');
        }

        if(array_key_exists($destinationCountryIso, $sourceMapping)) {
            return $sourceMapping[$destinationCountryIso];
        }

        return Zone::GLOBAL;
    }
}
