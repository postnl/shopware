<?php

namespace PostNL\Shopware6\Service\Shopware\DataExtractor;

use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\System\Country\CountryEntity;

class OrderDataExtractor
{
    protected $orderDeliveryDataExtractor;

    public function __construct(OrderDeliveryDataExtractor $orderDeliveryDataExtractor)
    {
        $this->orderDeliveryDataExtractor = $orderDeliveryDataExtractor;
    }

    public function extractAddresses(OrderEntity $order): OrderAddressCollection
    {
        $addresses = $order->getAddresses();

        if ($addresses instanceof OrderAddressCollection) {
            return $addresses;
        }

        // TODO Exception
        throw new \Exception('Could not extract addresses from order');
    }

    public function extractCustomer(OrderEntity $order): OrderCustomerEntity
    {
        $customer = $order->getOrderCustomer();

        if ($customer instanceof OrderCustomerEntity) {
            return $customer;
        }

        // TODO Exception
        throw new \Exception('Could not extract customer from order');
    }

    public function extractDeliveries(OrderEntity $order): OrderDeliveryCollection
    {
        $deliveries = $order->getDeliveries();

        if ($deliveries instanceof OrderDeliveryCollection) {
            return $deliveries;
        }

        // TODO Exception
        throw new \Exception('Could not extract deliveries from order');
    }

    public function extractDelivery(OrderEntity $order): OrderDeliveryEntity
    {
        $delivery = $this->extractDeliveries($order)->first();

        if($delivery instanceof OrderDeliveryEntity) {
            return $delivery;
        }

        // TODO Exception
        throw new \Exception('Could not extract delivery from order');
    }

    public function extractDeliveryAddress(OrderEntity $order): OrderAddressEntity
    {
        return $this->orderDeliveryDataExtractor->extractShippingAddress($this->extractDelivery($order));
    }

    public function extractDeliveryCountry(OrderEntity $order): CountryEntity
    {
        return $this->orderDeliveryDataExtractor->extractShippingCountry($this->extractDelivery($order));
    }
}
