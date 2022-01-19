<?php

namespace PostNL\Shipments\Service\Attribute\TypeHandler;

interface AttributeTypeHandlerInterface
{
    public function supports(): array;

    public function handle($data);
}
