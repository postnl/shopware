<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Entity\Request;

use Firstred\PostNL\Attribute\SerializableProperty;
use Firstred\PostNL\Entity\AbstractEntity;

class PostalCode extends AbstractEntity
{
    /** @var string|null $postalCode */
    #[SerializableProperty(type: 'string')]
    protected ?string $postalCode = null;

    /** @var int|null $houseNumber */
    #[SerializableProperty(type: 'int')]
    protected ?int $houseNumber = null;

    /** @var string|null $houseNumberAddition */
    #[SerializableProperty(type: 'string')]
    protected ?string $houseNumberAddition = null;

    /**
     * @param string|null $postalCode
     * @param int|null    $houseNumber
     * @param string|null $houseNumberAddition
     */
    public function __construct(?string $postalCode = null, ?int $houseNumber = null, ?string $houseNumberAddition = null)
    {
        parent::__construct();

        $this->setPostalCode($postalCode);
        $this->setHouseNumber($houseNumber);
        $this->setHouseNumberAddition($houseNumberAddition);
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getHouseNumber(): ?int
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?int $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    public function getHouseNumberAddition(): ?string
    {
        return $this->houseNumberAddition;
    }

    public function setHouseNumberAddition(?string $houseNumberAddition): void
    {
        $this->houseNumberAddition = $houseNumberAddition;
    }


    /**
     * This method returns a unique cache key for every unique cacheable request as defined by PSR-6.
     *
     * @see https://www.php-fig.org/psr/psr-6/#definitions
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        $cacheKey = "PostalCode.{$this->getPostalCode()}.{$this->getHouseNumber()}";

        $addition = $this->getHouseNumberAddition();

        if(!empty($addition)) {
            $cacheKey .= ".{$addition}";
        }

        return hash(
            algo: 'xxh128',
            data: $cacheKey,
        );
    }
}
