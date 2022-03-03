<?php

namespace PostNL\Shopware6\Service\Shopware\DataExtractor;

use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryEntity;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\System\Country\CountryEntity;

class OrderDeliveryDataExtractor
{
    protected $orderAddressDataExtractor;

    public function __construct(OrderAddressDataExtractor $orderAddressDataExtractor)
    {
        $this->orderAddressDataExtractor = $orderAddressDataExtractor;
    }

    public function extractShippingMethod(OrderDeliveryEntity $delivery): ShippingMethodEntity
    {
        $shippingMethod = $delivery->getShippingMethod();

        if($shippingMethod instanceof ShippingMethodEntity) {
            return $shippingMethod;
        }

        // TODO Exception
        throw new \Exception('Could not extract shipping method');
    }

    public function extractShippingAddress(OrderDeliveryEntity $delivery): OrderAddressEntity
    {
        $shippingAddress = $delivery->getShippingOrderAddress();

        if($shippingAddress instanceof OrderAddressEntity){
            return $shippingAddress;
        }

        // TODO Exception
        throw new \Exception('Could not extract shipping address');
    }

    public function extractShippingCountry(OrderDeliveryEntity $deliveryEntity): CountryEntity
    {
        return $this->orderAddressDataExtractor->extractCountry($this->extractShippingAddress($deliveryEntity));
    }
}
