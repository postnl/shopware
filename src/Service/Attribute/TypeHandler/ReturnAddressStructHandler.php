<?php

namespace PostNL\Shopware6\Service\Attribute\TypeHandler;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\Shopware\CountryService;
use PostNL\Shopware6\Struct\Config\ReturnAddressStruct;
use PostNL\Shopware6\Struct\Config\SenderAddressStruct;
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

        return $this->attributeFactory->create(
            ReturnAddressStruct::class,
            $data,
            $context
        );
    }

}
