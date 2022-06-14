<?php declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Config;

class ReturnAddressStruct extends ApiCompatibleStruct
{
    /**
     * @var bool
     */
    protected $hasReturnContract;

    /**
     * @var string
     */
    protected $companyName;

    /**
     * @var string|null
     */
    protected $street;

    /**
     * @var string|null
     */
    protected $houseNr;

    /**
     * @var string|null
     */
    protected $houseNrExt;

    /**
     * @var string
     */
    protected $zipcode;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $countrycode;

    /**
     * @var string
     */
    protected $returnCustomerCode;

    /**
     * @var string|null
     */
    protected $returnNumber;

    /**
     * @return bool
     */
    public function isHasReturnContract(): bool
    {
        return $this->hasReturnContract;
    }

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        if($this->getCountrycode() === 'NL' && $this->isHasReturnContract()) {
            return 'Antwoordnummer '. $this->getReturnNumber();
        }

        return $this->street;
    }

    /**
     * @return string|null
     */
    public function getHouseNr(): ?string
    {
        return $this->houseNr;
    }

    /**
     * @return string|null
     */
    public function getHouseNrExt(): ?string
    {
        return $this->houseNrExt;
    }

    /**
     * @return string
     */
    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountrycode(): string
    {
        return $this->countrycode;
    }

    /**
     * @return string
     */
    public function getReturnCustomerCode(): string
    {
        return $this->returnCustomerCode;
    }

    /**
     * @return string|null
     */
    public function getReturnNumber(): ?string
    {
        return $this->returnNumber;
    }


}
