<?php

namespace PostNL\Shopware6\Service\Shopware\DataExtractor;

use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\System\Country\CountryEntity;

class OrderAddressDataExtractor
{
    public function extractCountry(OrderAddressEntity $address): CountryEntity
    {
        $country = $address->getCountry();

        if($country instanceof CountryEntity) {
            return $country;
        }

        // TODO Exception
        throw new \Exception('Could not extract country');
    }
}
