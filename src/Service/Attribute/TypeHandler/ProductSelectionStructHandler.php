<?php

namespace PostNL\Shipments\Service\Attribute\TypeHandler;

use PostNL\Shipments\Service\Attribute\AttributeStruct;
use PostNL\Shipments\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shipments\Struct\Config\CustomerDataStruct;
use PostNL\Shipments\Struct\Config\ProductSelectionStruct;
use Shopware\Core\Framework\Context;

class ProductSelectionStructHandler implements AttributeTypeHandlerInterface
{
    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    public function __construct(AttributeFactory $attributeFactory)
    {
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @inheritDoc
     */
    public function supports(): array
    {
        return [ProductSelectionStruct::class];
    }

    /**
     * @inheritDoc
     */
    public function handle($data, Context $context): AttributeStruct
    {
        if(is_null($data)) {
            return $this->attributeFactory->create(
                ProductSelectionStruct::class,
                [],
                $context
            );
        }
        return $this->attributeFactory->create(
            ProductSelectionStruct::class,
            json_decode($data, true),
            $context
        );
    }
}
