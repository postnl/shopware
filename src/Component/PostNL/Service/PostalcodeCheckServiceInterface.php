<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service;

use Firstred\PostNL\Entity\Request\GenerateBarcode;
use Firstred\PostNL\Exception\CifDownException;
use Firstred\PostNL\Exception\CifException;
use Firstred\PostNL\Exception\HttpClientException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use Firstred\PostNL\Service\ServiceInterface;

/**
 * @since 1.2.0
 */
interface PostalcodeCheckServiceInterface extends ServiceInterface
{
    public function postalcodeCheck($generateBarcode): string;
}
