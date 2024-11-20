<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\PostNL\Entity\Response;

use JsonSerializable;

class ActivateReturnErrorMessage implements JsonSerializable
{
    protected string $code;
    protected string $description;

    /**
     * @param string $code
     * @param string $description
     */
    public function __construct(string $code, string $description)
    {
        $this->code = $code;
        $this->description = $description;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'description' => $this->description,
        ];
    }
}