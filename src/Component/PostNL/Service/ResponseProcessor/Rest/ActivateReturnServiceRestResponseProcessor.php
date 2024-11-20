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
use PostNL\Shopware6\Component\PostNL\Entity\Response\ActivateReturnErrorMessage;
use PostNL\Shopware6\Component\PostNL\Entity\Response\ActivateReturnErrorResult;
use PostNL\Shopware6\Component\PostNL\Entity\Response\ActivateReturnResponse;
use PostNL\Shopware6\Component\PostNL\Service\ResponseProcessor\ActivateReturnServiceResponseProcessorInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @since 2.0.0
 *
 * @internal
 */
class ActivateReturnServiceRestResponseProcessor extends AbstractRestResponseProcessor implements ActivateReturnServiceResponseProcessorInterface
{
    /**
     * @param ResponseInterface $response
     *
     * @return ActivateReturnResponse
     *
     * @throws CifDownException
     * @throws CifException
     * @throws HttpClientException
     * @throws InvalidConfigurationException
     * @throws NotFoundException
     * @throws ResponseException
     * @since 2.0.0
     */
    public function processActivateReturnResponse(ResponseInterface $response): ActivateReturnResponse
    {
        $this->validateResponse(response: $response);
        $responseContent = $this->getResponseText(response: $response);

        try {
            $json = json_decode(json: $responseContent, flags: JSON_THROW_ON_ERROR);
        }
        catch (JsonException $e) {
            throw new ResponseException(message: 'Invalid API Response', previous: $e, response: $response);
        }

        if (empty($json)) {
            throw new NotFoundException();
        }

        $errorsPerBarcode = [];

        foreach ($json->errorsPerBarcode as $barcodeError) {
            $warnings = [];
            $errors = [];

            foreach ($barcodeError->warnings as $warning) {
                $warnings[] = new ActivateReturnErrorMessage(code: $warning->code, description: $warning->description);
            }

            foreach ($barcodeError->errors as $error) {
                $errors[] = new ActivateReturnErrorMessage(code: $error->code, description: $error->description);
            }

            $errorResult = new ActivateReturnErrorResult(
                barcode : $barcodeError->barcode,
                warnings: $warnings,
                errors  : $errors
            );

            $errorsPerBarcode[] = $errorResult;
        }

        return new ActivateReturnResponse($json->successFulBarcodes, $errorsPerBarcode);
    }

    protected function validateResponse(ResponseInterface $response): bool
    {
        parent::validateResponse($response);

        try {
            $body = json_decode(json: (string)$response->getBody(), associative: true, flags: JSON_THROW_ON_ERROR);
        }
        catch (JsonException $e) {
            throw new ResponseException(message: 'Invalid API response', previous: $e, response: $response);
        }

        if (!array_key_exists('successFulBarcodes', $body) || !array_key_exists('errorsPerBarcode', $body)) {
            throw new ResponseException(message: 'Invalid API response', response: $response);
        }

        return true;
    }
}
