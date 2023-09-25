<?php

namespace PostNL\Shopware6\Component\PostNL\Entity\Response;

class PostalCodeResponse
{
    /** @var PostalCodeResult[] */
    protected array $PostalCodeResult;

    /**
     *
     * @param array $PostalCodeResult
     */
    public function __construct(array $PostalCodeResult = [])
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
