<?php declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Config;

class ReturnAddressStruct extends ApiCompatibleStruct
{
    protected bool $hasReturnContract;
    protected bool $useHomeAddress;
    protected string $companyName;
    protected ?string $street;
    protected ?string $houseNr;
    protected ?string $houseNrExt;
    protected string $zipcode;
    protected string $city;
    protected string $countrycode;
    protected string $returnCustomerCode;
    protected ?string $returnNumber;

    public function isHasReturnContract(): bool
    {
        return $this->hasReturnContract;
    }

    public function isUseHomeAddress(): bool
    {
        return $this->useHomeAddress;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function getStreet(): ?string
    {
        if($this->getCountrycode() === 'NL' && $this->isHasReturnContract() && !$this->isUseHomeAddress()) {
            return 'Antwoordnummer '. $this->getReturnNumber();
        }

        return $this->street;
    }

    public function getHouseNr(): ?string
    {
        return $this->houseNr;
    }

    public function getHouseNrExt(): ?string
    {
        return $this->houseNrExt;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountrycode(): string
    {
        return $this->countrycode;
    }

    public function getReturnCustomerCode(): string
    {
        return $this->returnCustomerCode;
    }

    public function getReturnNumber(): ?string
    {
        return $this->returnNumber;
    }
}
