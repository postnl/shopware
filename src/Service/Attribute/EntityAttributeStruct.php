<?php

namespace PostNL\Shipments\Service\Attribute;

use PostNL\Shipments\Defaults;

abstract class EntityAttributeStruct extends AttributeStruct
{
    /**
     * @return string
     */
    abstract public function supports(): string;

    /**
     * @return array<mixed>
     */
    public function toCustomFields(): array
    {
        return [Defaults::CUSTOM_FIELDS_KEY => $this->toArray()];
    }
}
