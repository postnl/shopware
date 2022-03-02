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
     * @var string
     */
    protected $apiMode = 'production';

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

    //=========================================================================================================

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
    protected $productShipmentNlBeDefault;

    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentNlBeAlternative;

    /**
     * @var ProductSelectionStruct
     */
    protected $productPickupNlBeDefault;

    //====================================================================================================

    /**
     * @var bool
     */
    protected $debugMode = false;

    //====================================================================================================

    /**
     * @return string
     */
    public function getProductionApiKey(): string
    {
        return $this->productionApiKey;
    }

    /**
     * @return string
     */
    public function getSandboxApiKey(): string
    {
        return $this->sandboxApiKey;
    }

    /**
     * @return string
     */
    public function getApiMode(): string
    {
        return $this->apiMode;
    }

    /**
     * @return bool
     */
    public function isSandboxMode(): bool
    {
        return $this->getApiMode() === 'sandbox';
    }

    /**
     * @return CustomerDataStruct
     */
    public function getCustomerData(): CustomerDataStruct
    {
        return $this->customerData;
    }

    /**
     * @return SenderAddressStruct
     */
    public function getSenderAddress(): SenderAddressStruct
    {
        return $this->senderAddress;
    }

    //============================================================================================================

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlNlDefault(): ProductSelectionStruct
    {
        return $this->productShipmentNlNlDefault;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlNlAlternative(): ProductSelectionStruct
    {
        return $this->productShipmentNlNlAlternative;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductPickupNlNlDefault(): ProductSelectionStruct
    {
        return $this->productPickupNlNlDefault;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlBeDefault(): ProductSelectionStruct
    {
        return $this->productShipmentNlBeDefault;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlBeAlternative(): ProductSelectionStruct
    {
        return $this->productShipmentNlBeAlternative;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductPickupNlBeDefault(): ProductSelectionStruct
    {
        return $this->productPickupNlBeDefault;
    }

    //=========================================================================================================

    /**
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }
}
