<?php

namespace PostNL\Shipments\Service\Attribute\TypeHandler;

use PostNL\Shipments\Service\Attribute\AttributeFactory;
use PostNL\Shipments\Struct\Config\CustomerDataStruct;

class CustomerDataStructHandler implements AttributeTypeHandlerInterface
{
    protected $attributeFactory;

    public function __construct(AttributeFactory $attributeFactory)
    {
        $this->attributeFactory = $attributeFactory;
    }

    public function supports(): array
    {
        return [CustomerDataStruct::class];
    }

    public function handle($data)
    {
        return $this->attributeFactory->create(CustomerDataStruct::class, json_decode($data, true));
    }
}
