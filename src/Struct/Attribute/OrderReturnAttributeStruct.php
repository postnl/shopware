<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Attribute;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;

class OrderReturnAttributeStruct extends AttributeStruct
{
    protected ?bool $labelInTheBox = null;
    protected ?bool $shipmentAndReturn = null;
    protected ?bool $smartReturn = null;
    protected array $smartReturnBarcodes = [];

    public function getLabelInTheBox(): ?bool
    {
        return $this->labelInTheBox;
    }

    public function getShipmentAndReturn(): ?bool
    {
        return $this->shipmentAndReturn;
    }

    public function getSmartReturn(): ?bool
    {
        return $this->smartReturn;
    }

    public function getSmartReturnBarcodes(): array
    {
        return $this->smartReturnBarcodes;
    }
}