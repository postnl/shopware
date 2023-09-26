<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Struct;

use PostNL\Shopware6\Component\PostNL\Entity\Response\PostalCodeResult;
use Shopware\Core\Framework\Struct\Struct;

class PostalCodeStruct extends Struct
{
    public static function createFromPostalCodeResult(PostalCodeResult $result)
    {
        return (new static())->assign([
            'city' => $result->getCity(),
            'postalCode' => $result->getPostalCode(),
            'streetName' => $result->getStreetName(),
            'houseNumber' => $result->getHouseNumber(),
            'houseNumberAddition' => $result->getHouseNumberAddition(),
            'formattedAddress' => $result->getFormattedAddress(),
        ]);
    }

    protected string $city;
    protected string $postalCode;
    protected string $streetName;
    protected int $houseNumber;
    protected ?string $houseNumberAddition;
    /** @var string[] */
    protected array $formattedAddress = [];

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getStreetName(): string
    {
        return $this->streetName;
    }

    public function getHouseNumber(): int
    {
        return $this->houseNumber;
    }

    public function getHouseNumberAddition(): ?string
    {
        return $this->houseNumberAddition;
    }

    public function getFormattedAddress(): array
    {
        return $this->formattedAddress;
    }
}