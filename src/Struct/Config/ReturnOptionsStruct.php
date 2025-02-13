<?php declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Config;

class ReturnOptionsStruct extends ApiCompatibleStruct
{
    const T_NONE = 'none';
    const T_LABEL_IN_THE_BOX = 'labelInTheBox';
    const T_SHIPMENT_AND_RETURN = 'shipmentAndReturn';

    protected ?string $type = self::T_NONE;
    protected bool $allowImmediateShipmentAndReturn = false;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isAllowImmediateShipmentAndReturn(): bool
    {
        return $this->allowImmediateShipmentAndReturn;
    }
}
