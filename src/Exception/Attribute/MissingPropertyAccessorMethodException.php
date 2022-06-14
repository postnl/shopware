<?php declare(strict_types=1);

namespace PostNL\Shopware6\Exception\Attribute;

use Shopware\Core\Framework\ShopwareHttpException;

class MissingPropertyAccessorMethodException extends ShopwareHttpException
{
    /**
     * @param array<mixed> $parameters
     * @param \Throwable|null $e
     */
    public function __construct(array $parameters = [], ?\Throwable $e = null)
    {
        if(is_array($parameters['example'])) {
            $parameters['example'] = implode(', ', $parameters['example']);
        }

        $message = "Missing property accessor method for property \"{{property}}\" in class \"{{class}}\". Possible methods: {{example}}.";
        parent::__construct($message, $parameters, $e);
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return 'POSTNL__ATTRIBUTE_MISSING_PROPERTY_ACCESSOR';
    }
}
