<?php

namespace PostNL\Shopware6\Subscriber;

use PostNL\Shopware6\Defaults;
use Shopware\Core\Checkout\Customer\CustomerEvents;
use Shopware\Core\Framework\Event\DataMappingEvent;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
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

    public function onMappingAddress(DataMappingEvent $event): void
    {
        $inputData = $event->getInput();
        $outputData = $event->getOutput();

        $postnlData = $inputData->get(Defaults::CUSTOM_FIELDS_KEY);

        if(!$postnlData instanceof RequestDataBag) {
            return;
        }

        $outputData['customFields'] = array_merge_recursive($outputData['customFields'] ?? [], [
            Defaults::CUSTOM_FIELDS_KEY => [
                Defaults::CUSTOM_FIELDS_STREETNAME_KEY => $postnlData->get('streetName'),
                Defaults::CUSTOM_FIELDS_HOUSENUMBER_KEY => $postnlData->get('houseNumber'),
                Defaults::CUSTOM_FIELDS_HOUSENUMBER_ADDITION_KEY => $postnlData->get('houseNumberAddition'),
            ],
        ]);

        $event->setOutput($outputData);
    }
}
