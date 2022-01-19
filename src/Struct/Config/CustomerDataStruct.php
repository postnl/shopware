<?php declare(strict_types=1);

namespace PostNL\Shipments\Struct\Config;

use PostNL\Shipments\Service\Attribute\AttributeStruct;

class CustomerDataStruct extends AttributeStruct
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
    protected $collectionLocation = '';

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
     * @param string $customerNumber
     */
    public function setCustomerNumber(string $customerNumber): void
    {
        $this->customerNumber = $customerNumber;
    }

    /**
     * @return string
     */
    public function getCustomerCode(): string
    {
        return $this->customerCode;
    }

    /**
     * @param string $customerCode
     */
    public function setCustomerCode(string $customerCode): void
    {
        $this->customerCode = $customerCode;
    }

    /**
     * @return string
     */
    public function getCollectionLocation(): string
    {
        return $this->collectionLocation;
    }

    /**
     * @param string $collectionLocation
     */
    public function setCollectionLocation(string $collectionLocation): void
    {
        $this->collectionLocation = $collectionLocation;
    }

    /**
     * @return string
     */
    public function getGlobalPackCustomerCode(): string
    {
        return $this->globalPackCustomerCode;
    }

    /**
     * @param string $globalPackCustomerCode
     */
    public function setGlobalPackCustomerCode(string $globalPackCustomerCode): void
    {
        $this->globalPackCustomerCode = $globalPackCustomerCode;
    }

    /**
     * @return string
     */
    public function getGlobalPackBarcodeType(): string
    {
        return $this->globalPackBarcodeType;
    }

    /**
     * @param string $globalPackBarcodeType
     */
    public function setGlobalPackBarcodeType(string $globalPackBarcodeType): void
    {
        $this->globalPackBarcodeType = $globalPackBarcodeType;
    }


}
