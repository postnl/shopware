<?php

namespace PostNL\Shipments\Struct\Config;

use PostNL\Shipments\Service\Attribute\AttributeStruct;

abstract class ApiCompatibleStruct extends AttributeStruct
{
    public function getVarsForApi(): array
    {
        $vars = [];
        foreach($this->getVars() as $key => $value) {
            $vars[ucfirst($key)] = $value;
        }

        return $vars;
    }
}
