<?php

namespace PostNL\Shopware6\Service\Attribute\TypeHandler;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Struct\Attribute\OrderReturnAttributeStruct;
use Shopware\Core\Framework\Context;

class OrderReturnStructHandler implements AttributeTypeHandlerInterface
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
        return [OrderReturnAttributeStruct::class];
    }

    public function handle($data, Context $context): AttributeStruct
    {
        if (is_null($data)) {
            return $this->attributeFactory->create(
                OrderReturnAttributeStruct::class,
                [],
                $context
            );
        }
        return $this->attributeFactory->create(
            OrderReturnAttributeStruct::class,
            json_decode($data, true),
            $context
        );
    }
}
