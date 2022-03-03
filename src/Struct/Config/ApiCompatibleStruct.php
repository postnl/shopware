<?php

namespace PostNL\Shopware6\Struct\Config;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;

abstract class ApiCompatibleStruct extends AttributeStruct
{
    /**
     * @return array<mixed>
     */
    public function getVarsForApi(): array
    {
        $vars = [];
        foreach($this->getVars() as $key => $value) {
            $vars[ucfirst($key)] = $value;
        }

        return $vars;
    }
}
