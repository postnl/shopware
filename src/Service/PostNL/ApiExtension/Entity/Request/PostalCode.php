<?php

namespace PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Request;

use Firstred\PostNL\Entity\AbstractEntity;

/**
 * Class SendShipment.
 *
 * @method string   getPostalCode()
 * @method string    getHouseNumber()
 * @method string|null getHouseNumberAddition()
 * @method PostalCode  setPostalCode(string $postalcode)
 * @method PostalCode  setHouseNumber(string $housenumber)
 * @method PostalCode  setHouseNumberAddition(string|null $housenumberaddition = null)
 *
 * @since 1.0.0
 */
class PostalCode extends AbstractEntity
{

    /** @var string */
    protected string $postalcode;
    /** @var string */
    protected string $housenumber;
    /** @var string|null */
    protected ?string $housenumberaddition;


    /**
     * SendShipment constructor.
     *
     * @param string $postalcode
     * @param string $housenumber
     * @param string|null $housenumberaddition
     */
    public function __construct(
        string $postalcode,
        string $housenumber,
        string $housenumberaddition = null
    ) {
        parent::__construct();

        $this->setPostalCode($postalcode);
        $this->setHouseNumber($housenumber);
        $this->setHouseNumberAddition($housenumberaddition);
    }

    /**
     * Return a serializable array for `json_encode`.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $json = [];
        if (!$this->currentService || !in_array($this->currentService, array_keys(static::$defaultProperties))) {
            return $json;
        }

        foreach (array_keys(static::$defaultProperties[$this->currentService]) as $propertyName) {
            if (isset($this->$propertyName)) {
                if ('Shipments' === $propertyName && count($this->$propertyName) >= 1) {
                    $properties = [];
                    foreach ($this->$propertyName as $property) {
                        $properties[] = $property;
                    }
                    $json[$propertyName] = $properties;
                } else {
                    $json[$propertyName] = $this->$propertyName;
                }
            }
        }

        return $json;
    }
}
