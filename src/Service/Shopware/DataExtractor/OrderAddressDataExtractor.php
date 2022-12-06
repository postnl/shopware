<?php

namespace PostNL\Shopware6\Service\Shopware\DataExtractor;

use PostNL\Shopware6\Defaults;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressCollection;
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

    public function filterByAddressType(OrderAddressCollection $addresses, string $addressType = "01"): OrderAddressCollection
    {
        /** @var OrderAddressCollection */
        return $addresses->filter(function(OrderAddressEntity $address) use ($addressType) {
            return $address->getCustomFields()[Defaults::CUSTOM_FIELDS_KEY]['addressType'] === $addressType;
        });
    }
}
