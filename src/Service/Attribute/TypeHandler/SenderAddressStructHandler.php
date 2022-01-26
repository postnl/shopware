<?php

namespace PostNL\Shipments\Service\Attribute\TypeHandler;

use PostNL\Shipments\Service\Attribute\AttributeStruct;
use PostNL\Shipments\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shipments\Service\Shopware\CountryService;
use PostNL\Shipments\Struct\Config\SenderAddressStruct;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;

class SenderAddressStructHandler implements AttributeTypeHandlerInterface
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
        return [SenderAddressStruct::class];
    }

    public function handle($data, Context $context): AttributeStruct
    {
        $data = json_decode($data, true);

        if (Uuid::isValid($data['country'])) {
            $data['countrycode'] = $this->countryService->getCountryCodeById($data['country'], $context);
        } else {
            $data['countrycode'] = '';
        }
        unset($data['country']);

        return $this->attributeFactory->create(
            SenderAddressStruct::class,
            $data,
            $context
        );
    }

}
