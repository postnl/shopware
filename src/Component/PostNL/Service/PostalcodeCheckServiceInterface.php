<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service;

use Firstred\PostNL\Exception\CifDownException;
use Firstred\PostNL\Exception\CifException;
use Firstred\PostNL\Exception\HttpClientException;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use Firstred\PostNL\Service\ServiceInterface;
use PostNL\Shopware6\Component\PostNL\Entity\Request\PostalCode;
use PostNL\Shopware6\Component\PostNL\Entity\Response\PostalCodeResponse;

interface PostalcodeCheckServiceInterface extends ServiceInterface
{
    /**
     * @param PostalCode $postalCode
     * @return string
     * @throws CifDownException
     * @throws CifException
     * @throws HttpClientException
     * @throws ResponseException
     * @throws InvalidConfigurationException
     * @throws InvalidArgumentException
     */
    public function postalcodeCheck(PostalCode $postalCode): PostalCodeResponse;
}
