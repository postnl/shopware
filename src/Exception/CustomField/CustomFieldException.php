<?php

namespace PostNL\Shopware6\Exception\CustomField;

use Shopware\Core\Framework\ShopwareException;

abstract class CustomFieldException extends \RuntimeException implements ShopwareException
{
    /**
     * @var array
     */
    protected $parameters = [];

    public function __construct(string $message, array $parameters = [], ?\Throwable $previous = null)
    {
        $this->parameters = $parameters;
        $message = $this->parse($message, $parameters);

        parent::__construct($message, 0, $previous);
    }

    public function getErrors(bool $withTrace = false): \Generator
    {
        yield $this->getCommonErrorData($withTrace);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    protected function getCommonErrorData(bool $withTrace = false): array
    {
        $error = [
            'code' => $this->getErrorCode(),
            'detail' => $this->getMessage(),
            'meta' => [
                'parameters' => $this->getParameters(),
            ],
        ];

        if ($withTrace) {
            $error['trace'] = $this->getTrace();
        }

        return $error;
    }

    protected function parse(string $message, array $parameters = []): string
    {
        $regex = [];
        foreach ($parameters as $key => $value) {
            if (\is_array($value)) {
                continue;
            }

            $key = preg_replace('/[^a-z]/i', '', $key);
            $regex[sprintf('/\{\{(\s+)?(%s)(\s+)?\}\}/', $key)] = $value;
        }

        return preg_replace(array_keys($regex), array_values($regex), $message);
    }
}
