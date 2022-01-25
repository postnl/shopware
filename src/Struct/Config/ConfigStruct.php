<?php declare(strict_types=1);

namespace PostNL\Shipments\Struct\Config;

use PostNL\Shipments\Service\Attribute\AttributeStruct;

class ConfigStruct extends AttributeStruct
{
    /**
     * @var string
     */
    protected $productionApiKey = '';

    /**
     * @var string
     */
    protected $sandboxApiKey = '';

    /**
     * @var bool
     */
    protected $sandboxMode = false;

    /**
     * @var bool
     */
    protected $debugMode = false;

    /**
     * @var CustomerDataStruct
     */
    protected $customerData;

    /**
     * @var SenderAddressStruct
     */
    protected $senderAddress;

//    /**
//     * @var
//     */
//    protected $returnAddress;

    /**
     * @return string
     */
    public function getProductionApiKey(): string
    {
        return $this->productionApiKey;
    }

    /**
     * @param string $productionApiKey
     */
    public function setProductionApiKey(string $productionApiKey): void
    {
        $this->productionApiKey = $productionApiKey;
    }

    /**
     * @return string
     */
    public function getSandboxApiKey(): string
    {
        return $this->sandboxApiKey;
    }

    /**
     * @param string $sandboxApiKey
     */
    public function setSandboxApiKey(string $sandboxApiKey): void
    {
        $this->sandboxApiKey = $sandboxApiKey;
    }

    /**
     * @return bool
     */
    public function isSandboxMode(): bool
    {
        return $this->sandboxMode;
    }

    /**
     * @param bool $sandboxMode
     */
    public function setSandboxMode(bool $sandboxMode): void
    {
        $this->sandboxMode = $sandboxMode;
    }

    /**
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    /**
     * @param bool $debugMode
     */
    public function setDebugMode(bool $debugMode): void
    {
        $this->debugMode = $debugMode;
    }

    /**
     * @return CustomerDataStruct
     */
    public function getCustomerData(): CustomerDataStruct
    {
        return $this->customerData;
    }

    /**
     * @param CustomerDataStruct $customerData
     */
    public function setCustomerData(CustomerDataStruct $customerData): void
    {
        $this->customerData = $customerData;
    }

    /**
     * @return SenderAddressStruct
     */
    public function getSenderAddress(): SenderAddressStruct
    {
        return $this->senderAddress;
    }

    /**
     * @param SenderAddressStruct $senderAddress
     */
    public function setSenderAddress(SenderAddressStruct $senderAddress): void
    {
        $this->senderAddress = $senderAddress;
    }


}
