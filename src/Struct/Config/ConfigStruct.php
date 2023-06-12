<?php declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Config;

use PostNL\Shopware6\Service\Attribute\AttributeStruct;

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

    /**
     * @var ReturnAddressStruct
     */
    protected $returnAddress;

    //=========================================================================================================

    /**
     * @var bool
     */
    protected $addressCheck;

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

    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentBeBeDefault;

    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentBeBeAlternative;

    /**
     * @var ProductSelectionStruct
     */
    protected $productPickupBeBeDefault;

    //====================================================================================================

    /**
     * @var bool
     */
    protected $sendToEU = false;

    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentNlEuDefault;

    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentBeEuDefault;

    /**
     * @var bool
     */
    protected $sendToWorld = false;

    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentNlGlobalDefault;

    /**
     * @var ProductSelectionStruct
     */
    protected $productShipmentBeGlobalDefault;

    //====================================================================================================

    /**
     * @var string|null
     */
    protected $fallbackHSCode;
    /**
     * @var bool
     */
    protected $returnLabelInTheBox;

    //====================================================================================================

    /**
     * @var string
     */
    protected $printerFormat;

    /**
     * @var string
     */
    protected $printerFile;

    /**
     * @var string
     */
    protected $printerDPI;


    //====================================================================================================

    /**
     * @var string
     */
    protected $cutOffTime;

    /**
     * @var int
     */
    protected $shippingDuration = 1;

    /**
     * @var array<string>
     */
    protected $dropoffDays;

    /**
     * @var bool
     */
    protected $eveningDelivery = false;

    /**
     * @var float
     */
    protected $eveningSurcharge = 0.0;

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

    /**
     * @return ReturnAddressStruct
     */
    public function getReturnAddress(): ReturnAddressStruct
    {
        return $this->returnAddress;
    }

    //=========================================================================================================

    /**
     * @return bool
     */
    public function isAddressCheck(): bool
    {
        return $this->addressCheck;
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

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentBeBeDefault(): ProductSelectionStruct
    {
        return $this->productShipmentBeBeDefault;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentBeBeAlternative(): ProductSelectionStruct
    {
        return $this->productShipmentBeBeAlternative;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductPickupBeBeDefault(): ProductSelectionStruct
    {
        return $this->productPickupBeBeDefault;
    }

    //=========================================================================================================

    /**
     * @return bool
     */
    public function isSendToEU(): bool
    {
        return $this->sendToEU;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlEuDefault(): ProductSelectionStruct
    {
        return $this->productShipmentNlEuDefault;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentBeEuDefault(): ProductSelectionStruct
    {
        return $this->productShipmentBeEuDefault;
    }

    /**
     * @return bool
     */
    public function isSendToWorld(): bool
    {
        return $this->sendToWorld;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentNlGlobalDefault(): ProductSelectionStruct
    {
        return $this->productShipmentNlGlobalDefault;
    }

    /**
     * @return ProductSelectionStruct
     */
    public function getProductShipmentBeGlobalDefault(): ProductSelectionStruct
    {
        return $this->productShipmentBeGlobalDefault;
    }

    //=========================================================================================================

    /**
     * @return string|null
     */
    public function getFallbackHSCode(): ?string
    {
        return $this->fallbackHSCode;
    }

    /**
     * @return bool
     */
    public function isReturnLabelInTheBox(): bool
    {
        return $this->returnLabelInTheBox;
    }

    //=========================================================================================================

    /**
     * @return string
     */
    public function getPrinterFormat(): string
    {
        return $this->printerFormat;
    }

    /**
     * @return string
     */
    public function getPrinterFile(): string
    {
        return $this->printerFile;
    }

    /**
     * @return string
     */
    public function getPrinterDPI(): string
    {
        return $this->printerDPI;
    }

    //=========================================================================================================

    /**
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }


    //=========================================================================================================

    /**
     * @return string
     */
    public function getCutOffTime(): string
    {
        return $this->cutOffTime ?: '17:00:00';
    }

    /**
     * @return int
     */
    public function getShippingDuration(): int
    {
        return $this->shippingDuration;
    }

    /**
     * @return array<string>
     */
    public function getDropoffDays(): array
    {
        return $this->dropoffDays;
    }

    /**
     * @return bool
     */
    public function getEveningDelivery(): bool
    {
        return $this->eveningDelivery;
    }

    /**
     * @return float
     */
    public function getEveningSurcharge(): float
    {
        return $this->eveningSurcharge;
    }

    public function getAllowSundaySorting(): bool
    {
        return in_array(7, $this->getDropoffDays());
    }

    public function getDeliveryOptions(): array
    {
        //Check Development:GUIDELINES https://developer.postnl.nl/browse-apis/delivery-options/deliverydate-webservice/
        //Options: Daytime | Evening | Morning | Noon | Today | Sunday | Sameday | Afternoon
        //Combinations work contrary to the documentation
        $deliveryOptions = ['Daytime'];

        if ($this->getEveningDelivery()) {
            $deliveryOptions[] = 'Evening';
        }
        return $deliveryOptions;
    }
}
