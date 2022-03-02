<?php

namespace PostNL\Shipments\Service\Attribute\TypeHandler;

use PostNL\Shipments\Service\Attribute\AttributeStruct;
use PostNL\Shipments\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shipments\Service\Shopware\CountryService;
use PostNL\Shipments\Struct\Config\ReturnAddressStruct;
use PostNL\Shipments\Struct\Config\SenderAddressStruct;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;

class ReturnAddressStructHandler implements AttributeTypeHandlerInterface
{
    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var CountryService
     */
    protected $countryService;

    public function __construct(
        AttributeFactory $attributeFactory,
        CountryService   $countryService
    )
    {
        $this->attributeFactory = $attributeFactory;
        $this->countryService = $countryService;
    }

    public function supports(): array
    {
        return [ReturnAddressStruct::class];
    }

    public function handle($data, Context $context): AttributeStruct
    {
        if(!is_null($data)) {
            $data = json_decode($data, true);
        } else {
            $data = [];
        }
dd($data);
        if (array_key_exists('country', $data) && Uuid::isValid($data['country'])) {
            $data['countrycode'] = $this->countryService->getCountryCodeById($data['country'], $context);
        } else {
            $data['countrycode'] = '';
        }
        unset($data['country']);

        return $this->attributeFactory->create(
            ReturnAddressStruct::class,
            $data,
            $context
        );
    }

}
