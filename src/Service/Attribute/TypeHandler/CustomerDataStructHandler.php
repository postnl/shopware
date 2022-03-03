<?php

namespace PostNL\Shopware6\Service\Attribute\TypeHandler;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Struct\Config\CustomerDataStruct;
use Shopware\Core\Framework\Context;

class CustomerDataStructHandler implements AttributeTypeHandlerInterface
{
    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    public function __construct(AttributeFactory $attributeFactory)
    {
        $this->attributeFactory = $attributeFactory;
    }

    public function supports(): array
    {
        return [CustomerDataStruct::class];
    }

    public function handle($data, Context $context): AttributeStruct
    {
        if(is_null($data)) {
            return $this->attributeFactory->create(
                CustomerDataStruct::class,
                [],
                $context
            );
        }
        return $this->attributeFactory->create(
            CustomerDataStruct::class,
            json_decode($data, true),
            $context
        );
    }
}
