<?php

namespace PostNL\Shopware6\Service\PostNL\Api;

use Firstred\PostNL\PostNL;

use PostNL\Shopware6\Service\PostNL\Api\Entity\Request\PostalCode;
use PostNL\Shopware6\Service\PostNL\Api\Entity\Response\PostalCodeResponse;
use PostNL\Shopware6\Service\PostNL\Api\Exception\AddressNotFoundException;
use PostNL\Shopware6\Service\PostNL\Api\Service\PostalCodeCheckService;
use PostNL\Shopware6\Service\PostNL\Api\Service\PostalCodeCheckServiceInterface;

class PostNLExtension extends PostNL
{
    /** @var PostalCodeCheckServiceInterface */
    protected $postalCodeCheckService;


    /**
     * @return PostalCodeCheckServiceInterface
     */
    public function getPostalCodeCheckService(): PostalCodeCheckServiceInterface
    {
        if (!$this->postalCodeCheckService) {
            $this->setPostalCodeCheckService(new PostalCodeCheckService($this));
        }
        return $this->postalCodeCheckService;
    }

    /**
     * @param PostalCodeCheckServiceInterface $postalCodeCheckService
     */
    public function setPostalCodeCheckService(PostalCodeCheckServiceInterface $postalCodeCheckService): void
    {
        $this->postalCodeCheckService = $postalCodeCheckService;
    }

    /**
     * @param string $postalCode
     * @param string $houseNumber
     * @param string|null $houseNumberAddition
     * @return PostalCodeResponse
     * @throws Exception\InvalidAddressException|AddressNotFoundException
     */
    public function getPostalCode(string $postalCode, string $houseNumber, string $houseNumberAddition = null): PostalCodeResponse
    {
        return $this->getPostalCodeCheckService()->sendPostalCodeCheckRest(new PostalCode($postalCode,$houseNumber,$houseNumberAddition));
    }
}
