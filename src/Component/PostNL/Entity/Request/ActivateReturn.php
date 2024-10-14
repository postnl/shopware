<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Entity\Request;

use Firstred\PostNL\Attribute\SerializableProperty;
use Firstred\PostNL\Entity\AbstractEntity;

class ActivateReturn extends AbstractEntity
{
    #[SerializableProperty(type: 'string')]
    protected ?string $customerNumber = null;

    #[SerializableProperty(type: 'string')]
    protected ?string $customerCode = null;

    #[SerializableProperty(type: 'string')]
    protected ?string $barcode = null;

    public function __construct(
        ?string $customerNumber = null,
        ?string $customerCode = null,
        ?string $barcode = null
    )
    {
        parent::__construct();

        $this->setCustomerNumber($customerNumber);
        $this->setCustomerCode($customerCode);
        $this->setBarcode($barcode);
    }

    public function getCustomerNumber(): ?string
    {
        return $this->customerNumber;
    }

    public function setCustomerNumber(?string $customerNumber): void
    {
        $this->customerNumber = $customerNumber;
    }

    public function getCustomerCode(): ?string
    {
        return $this->customerCode;
    }

    public function setCustomerCode(?string $customerCode): void
    {
        $this->customerCode = $customerCode;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): void
    {
        $this->barcode = $barcode;
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
        $cacheKey = sprintf(
            'ActivateReturn.%s.%s.%s',
            $this->getCustomerNumber(),
            $this->getCustomerCode(),
            $this->getBarcode()
        );

        return hash(
            algo: 'xxh128',
            data: $cacheKey,
        );
    }
}
