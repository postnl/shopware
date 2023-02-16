<?php declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Config;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;

class CustomerDataStruct extends ApiCompatibleStruct
{
    /**
     * @var string
     */
    protected $customerNumber = '';

    /**
     * @var string
     */
    protected $customerCode = '';

    /**
     * @var string
     */
    protected $globalPackCustomerCode = '';

    /**
     * @var string
     */
    protected $globalPackBarcodeType = '';

    /**
     * @return string
     */
    public function getCustomerNumber(): string
    {
        return $this->customerNumber;
    }

    /**
     * @return string
     */
    public function getCustomerCode(): string
    {
        return $this->customerCode;
    }

    /**
     * @return string
     */
    public function getGlobalPackCustomerCode(): string
    {
        return $this->globalPackCustomerCode;
    }

    /**
     * @return string
     */
    public function getGlobalPackBarcodeType(): string
    {
        return $this->globalPackBarcodeType;
    }
}
