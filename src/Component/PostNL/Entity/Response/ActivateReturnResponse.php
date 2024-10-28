<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Entity\Response;

use JsonSerializable;

class ActivateReturnResponse implements JsonSerializable
{
    /** @var string[] */
    protected array $successfulBarcodes = [];

    /** @var ActivateReturnErrorResult[] */
    protected array $errorsPerBarcode = [];

    /**
     * @param string[]                    $successfulBarcodes
     * @param ActivateReturnErrorResult[] $errorsPerBarcode
     */
    public function __construct(array $successfulBarcodes, array $errorsPerBarcode)
    {
        $this->successfulBarcodes = $successfulBarcodes;
        $this->errorsPerBarcode = $errorsPerBarcode;
    }

    public function getSuccessfulBarcodes(): array
    {
        return $this->successfulBarcodes;
    }

    /**
     * @return ActivateReturnErrorResult[]
     */
    public function getErrorsPerBarcode(): array
    {
        return $this->errorsPerBarcode;
    }

    public function merge(...$responses): void
    {
        foreach ($responses as $response) {
            $this->successfulBarcodes = array_merge($this->successfulBarcodes, $response->getSuccessfulBarcodes());
            $this->errorsPerBarcode = array_merge($this->errorsPerBarcode, $response->getErrorsPerBarcode());
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'successfulBarcodes' => $this->successfulBarcodes,
            'errorsPerBarcode' => $this->errorsPerBarcode,
        ];
    }
}