<?php declare(strict_types=1);

namespace PostNL\Shipments\Service\PostNL\Delivery;

interface DeliveryType
{
    const SHIPMENT = "shipment";
    const PICKUP = "pickup";
    const MAILBOX = "mailbox";
}
