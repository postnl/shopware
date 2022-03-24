<?php

namespace PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Response;

use Firstred\PostNL\Entity\AbstractEntity;
use PostNL\Shopware6\Service\PostNL\PostalCodeService;


/**
 * @method string getCity()
 * @method string getPostalCode()
 * @method string getStreetName()
 * @method string getHouseNumber()
 * @method string|null getHouseNumberAddition()
 * @method string getFormattedAddress()
 *
 * @method PostalCodeResponse setCity(string $city)
 * @method PostalCodeResponse setPostalCode(string $postalCode)
 * @method PostalCodeResponse setStreetName(string $streetName)
 * @method PostalCodeResponse setHouseNumber(int $houseNumber)
 * @method PostalCodeResponse setHouseNumberAddition(string|null $houseNumberAddition)
 * @method PostalCodeResponse setFormattedAddress(array $formattedAddress)
 *
 */
class PostalCodeResponse extends AbstractEntity
{
    public static $defaultProperties = [
        'PostalCode' => [
            'MergedLabels' => PostalCodeService::DOMAIN_NAMESPACE,
            'ResponseShipments' => PostalCodeService::DOMAIN_NAMESPACE,
        ]
    ];


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
        parent::__construct();

        $this->setCity($city);
        $this->setPostalCode($postalCode);
        $this->setStreetName($streetName);
        $this->setHouseNumber($houseNumber);
        $this->setHouseNumberAddition($houseNumberAddition);
        $this->setFormattedAddress($formattedAddress);
    }
}
