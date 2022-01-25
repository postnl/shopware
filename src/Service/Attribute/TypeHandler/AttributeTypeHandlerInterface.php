<?php

namespace PostNL\Shipments\Service\Attribute\TypeHandler;

use PostNL\Shipments\Service\Attribute\AttributeStruct;
use Shopware\Core\Framework\Context;

interface AttributeTypeHandlerInterface
{
    /**
     * @return array
     */
    public function supports(): array;

    /**
     * @param $data
     * @param Context $context
     * @return AttributeStruct
     */
    public function handle($data, Context $context): AttributeStruct;
}
