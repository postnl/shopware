<?php

namespace PostNL\Shopware6\Service\PostNL\Api\Service;

use Firstred\PostNL\Service\ServiceInterface;
use PostNL\Shopware6\Service\PostNL\Api\Entity\Request\PostalCode;
use PostNL\Shopware6\Service\PostNL\Api\Entity\Response\PostalCodeResponse;
use PostNL\Shopware6\Service\PostNL\Api\Exception\AddressNotFoundException;
use PostNL\Shopware6\Service\PostNL\Api\Exception\InvalidAddressException;


interface PostalCodeCheckServiceInterface extends ServiceInterface
{
    /**
     * @param PostalCode $postalCode
     * @throws InvalidAddressException
     * @throws AddressNotFoundException
     * @return PostalCodeResponse
     */
    public function sendPostalCodeCheckRest(PostalCode $postalCode): PostalCodeResponse;
}
