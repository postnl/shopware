<?php

namespace PostNL\Shopware6\Service\Attribute\TypeHandler;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;
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
