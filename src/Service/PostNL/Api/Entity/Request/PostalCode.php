<?php

namespace PostNL\Shopware6\Service\PostNL\Api\Entity\Request;

use Firstred\PostNL\Entity\AbstractEntity;
use PostNL\Shopware6\Service\PostNL\PostalCodeService;

/**
 * Class SendShipment.
 *
 * @method string   getpostalcode()
 * @method string    gethousenumber()
 * @method string|null gethousenumberaddition()
 * @method PostalCode  setpostalcode(string $postalcode)
 * @method PostalCode  sethousenumber(string $housenumber)
 * @method PostalCode  sethousenumberaddition(string|null $housenumberaddition = null)
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

    public static $defaultProperties = [
        'PostalCodeCheck' => [
            'postalcode' => PostalCodeService::DOMAIN_NAMESPACE,
            'housenumber' => PostalCodeService::DOMAIN_NAMESPACE,
            'housenumberaddition' => PostalCodeService::DOMAIN_NAMESPACE
        ]
    ];

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
    )
    {
        parent::__construct();

        $this->setpostalcode($postalcode);
        $this->sethousenumber($housenumber);
        $this->sethousenumberaddition($housenumberaddition);
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
