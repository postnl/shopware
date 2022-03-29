<?php

namespace PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Response;


//
class PostalCodeResult
{

    /** @var string */
    protected string $city;
    /** @var string */
    protected string $postalCode;
    /** @var string */
    protected string $streetName;
    /** @var int */
    protected int $houseNumber;
    /** @var string | null */
    protected ?string $houseNumberAddition;
    /** @var string[] */
    protected array $formattedAddress;

    /**
     * SendShipmentResponse constructor.
     *
     * @param string $city
     * @param string $postalCode
     * @param string $streetName
     * @param int $houseNumber
     * @param string[] $formattedAddress
     * @param string|null $houseNumberAddition
     */
    public function __construct(
        string $city,
        string $postalCode,
        string $streetName,
        int    $houseNumber,
        array  $formattedAddress,
        string $houseNumberAddition = null)
    {

        $this->setCity($city);
        $this->setPostalCode($postalCode);
        $this->setStreetName($streetName);
        $this->setHouseNumber($houseNumber);
        $this->setHouseNumberAddition($houseNumberAddition);
        $this->setFormattedAddress($formattedAddress);
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
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getStreetName(): string
    {
        return $this->streetName;
    }

    /**
     * @param string $streetName
     */
    public function setStreetName(string $streetName): void
    {
        $this->streetName = $streetName;
    }

    /**
     * @return int
     */
    public function getHouseNumber(): int
    {
        return $this->houseNumber;
    }

    /**
     * @param int $houseNumber
     */
    public function setHouseNumber(int $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    /**
     * @return string|null
     */
    public function getHouseNumberAddition(): ?string
    {
        return $this->houseNumberAddition;
    }

    /**
     * @param string|null $houseNumberAddition
     */
    public function setHouseNumberAddition(?string $houseNumberAddition): void
    {
        $this->houseNumberAddition = $houseNumberAddition;
    }

    /**
     * @return string[]
     */
    public function getFormattedAddress(): array
    {
        return $this->formattedAddress;
    }

    /**
     * @param string[] $formattedAddress
     */
    public function setFormattedAddress(array $formattedAddress): void
    {
        $this->formattedAddress = $formattedAddress;
    }

}
