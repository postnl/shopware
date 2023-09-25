<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Service\ResponseProcessor\Rest;

use Firstred\PostNL\Exception\CifDownException;
use Firstred\PostNL\Exception\CifException;
use Firstred\PostNL\Exception\HttpClientException;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\NotFoundException;
use Firstred\PostNL\Exception\ResponseException;
use Firstred\PostNL\Service\ResponseProcessor\Rest\AbstractRestResponseProcessor;
use JsonException;
use PostNL\Shopware6\Component\PostNL\Entity\Response\PostalCodeResponse;
use PostNL\Shopware6\Component\PostNL\Entity\Response\PostalCodeResult;
use PostNL\Shopware6\Component\PostNL\Service\ResponseProcessor\PostalcodeCheckServiceResponseProcessorInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @since 2.0.0
 *
 * @internal
 */
class PostalcodeCheckServiceRestResponseProcessor extends AbstractRestResponseProcessor implements PostalcodeCheckServiceResponseProcessorInterface
{
    /**
     * Process the 'postalcode check' server response.
     *
     * @param ResponseInterface $response
     *
     * @return string
     *
     * @throws CifDownException
     * @throws CifException
     * @throws HttpClientException
     * @throws ResponseException
     * @throws InvalidConfigurationException
     *
     * @since 2.0.0
     */
    public function processPostalcodeCheckResponse(ResponseInterface $response): PostalCodeResponse
    {
        $this->validateResponse(response: $response);
        $responseContent = $this->getResponseText(response: $response);

        try {
            $json = json_decode(json: $responseContent, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ResponseException(message: 'Invalid API Response', previous: $e, response: $response);
        }

        if (empty($json)) {
            throw new NotFoundException();
        }

        $results = [];

        foreach ($json as $result) {
            $results[] = new PostalCodeResult(
                city: $result->city,
                postalCode: $result->postalCode,
                streetName: $result->streetName,
                houseNumber: $result->houseNumber,
                formattedAddress: $result->formattedAddress,
                houseNumberAddition: $result->houseNumberAddition ?? null,
            );
        }

        return new PostalCodeResponse($results);
    }
}
