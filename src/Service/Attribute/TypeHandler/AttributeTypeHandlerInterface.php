<?php

namespace PostNL\Shopware6\Service\Attribute\TypeHandler;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Struct\Struct;

interface AttributeTypeHandlerInterface
{
    /**
     * @return array
     */
    public function supports(): array;

    /**
     * @param         $data
     * @param Context $context
     * @return Struct|null
     */
    public function handle($data, Context $context): ?Struct;
}
