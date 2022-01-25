<?php

namespace PostNL\Shipments\Struct\Config;

class SenderAddressStruct extends ApiCompatibleStruct
{
    /**
     * @var string
     */
    protected $companyName;

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $street;

    /**
     * @var string
     */
    protected $houseNr;

    /**
     * @var string
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
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getHouseNr(): string
    {
        return $this->houseNr;
    }

    /**
     * @param string $houseNr
     */
    public function setHouseNr(string $houseNr): void
    {
        $this->houseNr = $houseNr;
    }

    /**
     * @return string
     */
    public function getHouseNrExt(): string
    {
        return $this->houseNrExt;
    }

    /**
     * @param string $houseNrExt
     */
    public function setHouseNrExt(string $houseNrExt): void
    {
        $this->houseNrExt = $houseNrExt;
    }

    /**
     * @return string
     */
    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    /**
     * @param string $zipcode
     */
    public function setZipcode(string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountrycode(): string
    {
        return $this->countrycode;
    }

    /**
     * @param string $countrycode
     */
    public function setCountrycode(string $countrycode): void
    {
        $this->countrycode = $countrycode;
    }
}
