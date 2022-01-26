<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\PostNL\ProductCode\Zone;

class ZoneService
{
    public static function getDestinationZone($sourceCountryIso, $destinationCountryIso): string
    {
        switch($sourceCountryIso) {
            case 'NL':
                $sourceMapping = Mapping::SOURCE_NL;
                break;
            case 'BE':
                $sourceMapping = Mapping::SOURCE_BE;
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
