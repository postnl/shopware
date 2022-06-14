<?php declare(strict_types=1);

namespace PostNL\Shopware6\Struct\Config;

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
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getHouseNr(): string
    {
        return $this->houseNr;
    }

    /**
     * @return string
     */
    public function getHouseNrExt(): string
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
}
