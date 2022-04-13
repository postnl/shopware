<?php

namespace PostNL\Shopware6\Service\PostNL\ApiExtension\Service;

use Firstred\PostNL\Service\ServiceInterface;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Request\PostalCode;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Response\PostalCodeResponse;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Exception\AddressNotFoundException;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Exception\InvalidAddressException;


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
