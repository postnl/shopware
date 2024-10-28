<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Entity\Response;

class ActivateReturnErrorResult
{
    protected string $barcode;

    /** @var ActivateReturnErrorMessage[] */
    protected array $warnings;

    /** @var ActivateReturnErrorMessage[] */
    protected array $errors;

    /**
     * @param string                       $barcode
     * @param ActivateReturnErrorMessage[] $warnings
     * @param ActivateReturnErrorMessage[] $errors
     */
    public function __construct(string $barcode, array $warnings, array $errors) {
        $this->barcode = $barcode;
        $this->warnings = $warnings;
        $this->errors = $errors;
    }

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    /**
     * @return ActivateReturnErrorMessage[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @return ActivateReturnErrorMessage[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}