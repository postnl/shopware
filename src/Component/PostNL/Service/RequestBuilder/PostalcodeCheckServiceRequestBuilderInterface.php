<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service\RequestBuilder;

use Firstred\PostNL\Entity\Request\GenerateBarcode;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use PostNL\Shopware6\Component\PostNL\Entity\Request\PostalCode;
use Psr\Http\Message\RequestInterface;

/**
 * @since 2.0.0
 *
 * @internal
 */
interface PostalcodeCheckServiceRequestBuilderInterface
{
    /**
     * Build the 'generate barcode' HTTP request.
     *
     * @param PostalCode $postalCode
     *
     * @return RequestInterface
     *
     * @throws InvalidArgumentException
     * @throws InvalidConfigurationException
     *
     * @since 2.0.0
     */
    public function buildPostalcodeCheckRequest(PostalCode $postalCode): RequestInterface;
}
