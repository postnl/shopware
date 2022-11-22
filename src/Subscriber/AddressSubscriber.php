<?php

namespace PostNL\Shopware6\Subscriber;

use PostNL\Shopware6\Defaults;
use Shopware\Core\Checkout\Customer\CustomerEvents;
use Shopware\Core\Framework\Event\DataMappingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddressSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            CustomerEvents::MAPPING_REGISTER_ADDRESS_BILLING  => 'onMappingAddress',
            CustomerEvents::MAPPING_REGISTER_ADDRESS_SHIPPING => 'onMappingAddress',
            CustomerEvents::MAPPING_ADDRESS_CREATE            => 'onMappingAddress',
        ];
    }

    public function onMappingAddress(DataMappingEvent $event): bool
    {
        $inputData = $event->getInput();
        $outputData = $event->getOutput();

        $outputData['customFields'] = array_merge_recursive($outputData['customFields'] ?? [], [
            Defaults::CUSTOM_FIELDS_KEY => [
                'streetName'          => $inputData->get('streetName'),
                'houseNumber'         => $inputData->get('houseNumber'),
                'houseNumberAddition' => $inputData->get('houseNumberAddition'),
            ],
        ]);

        $event->setOutput($outputData);

        return true;
    }
}
