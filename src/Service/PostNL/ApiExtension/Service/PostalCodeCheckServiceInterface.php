<?php

namespace PostNL\Shopware6\Service\PostNL\ApiExtension\Service;

use Firstred\PostNL\Service\ServiceInterface;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Request\PostalCode;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Entity\Response\PostalCodeResponse;

interface PostalCodeCheckServiceInterface extends ServiceInterface
{
    public function sendPostalCodeCheckRest(PostalCode $postalCode): PostalCodeResponse;
}
