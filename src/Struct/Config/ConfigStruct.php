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
     * @var ProductSelectionStruct
     */
    protected $productShipmentNlNlDefault;
    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentNlNlAlternative;

    /**
     * @var ProductSelectionStruct
     */
    protected $productPickupNlNlDefault;

    /**
     * @var ProductSelectionStruct
     */
    protected $productPickupNlNlAlternative;

    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentNlBeDefault;

    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentNlBeAlternative;

    /**
     * @var ProductSelectionStruct
     */
    protected $productPickupNlBeDefault;

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

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlNlDefault(): ProductSelectionStruct
    {
        return $this->productShipmentNlNlDefault;
    }

    /**
     * @param ProductSelectionStruct $productShipmentNlNlDefault
     */
    public function setProductShipmentNlNlDefault(ProductSelectionStruct $productShipmentNlNlDefault): void
    {
        $this->productShipmentNlNlDefault = $productShipmentNlNlDefault;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlNlAlternative(): ProductSelectionStruct
    {
        return $this->productShipmentNlNlAlternative;
    }

    /**
     * @param ProductSelectionStruct $productShipmentNlNlAlternative
     */
    public function setProductShipmentNlNlAlternative(ProductSelectionStruct $productShipmentNlNlAlternative): void
    {
        $this->productShipmentNlNlAlternative = $productShipmentNlNlAlternative;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductPickupNlNlDefault(): ProductSelectionStruct
    {
        return $this->productPickupNlNlDefault;
    }

    /**
     * @param ProductSelectionStruct $productPickupNlNlDefault
     */
    public function setProductPickupNlNlDefault(ProductSelectionStruct $productPickupNlNlDefault): void
    {
        $this->productPickupNlNlDefault = $productPickupNlNlDefault;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductPickupNlNlAlternative(): ProductSelectionStruct
    {
        return $this->productPickupNlNlAlternative;
    }

    /**
     * @param ProductSelectionStruct $productPickupNlNlAlternative
     */
    public function setProductPickupNlNlAlternative(ProductSelectionStruct $productPickupNlNlAlternative): void
    {
        $this->productPickupNlNlAlternative = $productPickupNlNlAlternative;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlBeDefault(): ProductSelectionStruct
    {
        return $this->productShipmentNlBeDefault;
    }

    /**
     * @param ProductSelectionStruct $productShipmentNlBeDefault
     */
    public function setProductShipmentNlBeDefault(ProductSelectionStruct $productShipmentNlBeDefault): void
    {
        $this->productShipmentNlBeDefault = $productShipmentNlBeDefault;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlBeAlternative(): ProductSelectionStruct
    {
        return $this->productShipmentNlBeAlternative;
    }

    /**
     * @param ProductSelectionStruct $productShipmentNlBeAlternative
     */
    public function setProductShipmentNlBeAlternative(ProductSelectionStruct $productShipmentNlBeAlternative): void
    {
        $this->productShipmentNlBeAlternative = $productShipmentNlBeAlternative;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductPickupNlBeDefault(): ProductSelectionStruct
    {
        return $this->productPickupNlBeDefault;
    }

    /**
     * @param ProductSelectionStruct $productPickupNlBeDefault
     */
    public function setProductPickupNlBeDefault(ProductSelectionStruct $productPickupNlBeDefault): void
    {
        $this->productPickupNlBeDefault = $productPickupNlBeDefault;
    }
}
