<?php

namespace PostNL\Shopware6\Service\Attribute\TypeHandler;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\Shopware\CountryService;
use PostNL\Shopware6\Struct\Config\ReturnAddressStruct;
use PostNL\Shopware6\Struct\Config\ReturnOptionsStruct;
use Shopware\Core\Framework\Context;

class ReturnOptionsStructHandler implements AttributeTypeHandlerInterface
{
    protected AttributeFactory $attributeFactory;

    public function __construct(
        AttributeFactory $attributeFactory,
    )
    {
        $this->attributeFactory = $attributeFactory;
    }

    public function supports(): array
    {
        return [ReturnOptionsStruct::class];
    }

    public function handle($data, Context $context): AttributeStruct
    {
        if(!is_null($data)) {
            $data = json_decode($data, true);
        } else {
            $data = [];
        }

        return $this->attributeFactory->create(
            ReturnOptionsStruct::class,
            $data,
            $context
        );
    }
}
