<?php

namespace PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Response;

use Firstred\PostNL\Entity\AbstractEntity;
use PostNL\Shopware6\Service\PostNL\PostalCodeService;



class PostalCodeResponse
{
    /** @var PostalCodeResult[] */
    protected array $PostalCodeResult;

    /**
     *
     * @param array|null $PostalCodeResult
     */
    public function __construct(array $PostalCodeResult = null)
    {
        $this->setPostalCodeResult($PostalCodeResult);
    }

    /**
     * @return PostalCodeResult[]
     */
    public function getPostalCodeResult(): array
    {
        return $this->PostalCodeResult;
    }

    /**
     * @param PostalCodeResult[] $PostalCodeResult
     */
    public function setPostalCodeResult(array $PostalCodeResult): void
    {
        $this->PostalCodeResult = $PostalCodeResult;
    }



}
