<?php declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL\Delivery\Zone;

use PostNL\Shopware6\Exception\PostNL\InvalidSourceCountryException;

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
                throw new InvalidSourceCountryException([
                    'sourceCountryIso' => $sourceCountryIso
                ]);
        }

        if(array_key_exists($destinationCountryIso, $sourceMapping)) {
            return $sourceMapping[$destinationCountryIso];
        }

        return Zone::GLOBAL;
    }
}
