<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\PostNL\ProductCode\Zone;

interface Mapping
{
    // countries with n/a isocodes in comments do not have a country in Shopware by default

    const SOURCE_NL = [
        'NL' => Zone::NL, // Netherlands
        'BE' => Zone::BE, // Belgium
        'AT' => Zone::EU, // Austria
        'BG' => Zone::EU, // Bulgaria
        'HR' => Zone::EU, // Croatia
        'CY' => Zone::EU, // Cyprus
        'CZ' => Zone::EU, // Czech Republic
        'DK' => Zone::EU, // Denmark (excluding Faroe Islands (FO) or Greenland (GL))
        'EE' => Zone::EU, // Estonia
        'FI' => Zone::EU, // Finland
        'FR' => Zone::EU, // France (including Monaco (MC) and Corsica (n/a), excluding Andorra (AD))
        'MC' => Zone::EU, // Monaco (see France)
        'DE' => Zone::EU, // Germany
        'GR' => Zone::EU, // Greece
        'HU' => Zone::EU, // Hungary
        'IE' => Zone::EU, // Ireland
        'IT' => Zone::EU, // Italy (excluding San Marino (SM) and Vatican City aka the Holy See (VA))
        'LV' => Zone::EU, // Latvia
        'LT' => Zone::EU, // Lithuania
        'LU' => Zone::EU, // Luxembourg
        'PL' => Zone::EU, // Poland
        'PT' => Zone::EU, // Portugal (including Azores (n/a) and Madeira (n/a))
        'RO' => Zone::EU, // Romania
        'SK' => Zone::EU, // Slovakia
        'SI' => Zone::EU, // Slovenia
        'ES' => Zone::EU, // Spain (including Balearic Islands (n/a), excluding Canary Islands (n/a), Melilla (n/a) and Ceuta (n/a))
        'SE' => Zone::EU, // Sweden
    ];

    const SOURCE_BE = [
        'NL' => Zone::EU, // Netherlands
        'BE' => Zone::BE, // Belgium
        'AT' => Zone::EU, // Austria
        'BG' => Zone::EU, // Bulgaria
        'HR' => Zone::EU, // Croatia
        'CY' => Zone::EU, // Cyprus
        'CZ' => Zone::EU, // Czech Republic
        'DK' => Zone::EU, // Denmark (excluding Faroe Islands (FO) or Greenland (GL))
        'EE' => Zone::EU, // Estonia
        'FI' => Zone::EU, // Finland
        'FR' => Zone::EU, // France (including Monaco (MC) and Corsica (n/a), excluding Andorra (AD))
        'MC' => Zone::EU, // Monaco (see France)
        'DE' => Zone::EU, // Germany
        'GR' => Zone::EU, // Greece
        'HU' => Zone::EU, // Hungary
        'IE' => Zone::EU, // Ireland
        'IT' => Zone::EU, // Italy (excluding San Marino (SM) and Vatican City aka the Holy See (VA))
        'LV' => Zone::EU, // Latvia
        'LT' => Zone::EU, // Lithuania
        'LU' => Zone::EU, // Luxembourg
        'PL' => Zone::EU, // Poland
        'PT' => Zone::EU, // Portugal (including Azores (n/a) and Madeira (n/a))
        'RO' => Zone::EU, // Romania
        'SK' => Zone::EU, // Slovakia
        'SI' => Zone::EU, // Slovenia
        'ES' => Zone::EU, // Spain (including Balearic Islands (n/a), excluding Canary Islands (n/a), Melilla (n/a) and Ceuta (n/a))
        'SE' => Zone::EU, // Sweden
    ];
}
