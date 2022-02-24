<?php

namespace PostNL\Shipments\Struct\Config;

use PostNL\Shipments\Service\Attribute\AttributeStruct;

abstract class ApiCompatibleStruct extends AttributeStruct
{
    /**
     * @return array<mixed>
     */
    public function getVarsForApi(): array
    {
        $vars = [];

        dd($this->getVars());
        foreach($this->getVars() as $key => $value) {
            $vars[ucfirst($key)] = $value;
        }

        return $vars;
    }
}
