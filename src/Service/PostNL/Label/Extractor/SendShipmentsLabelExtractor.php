<?php

namespace PostNL\Shopware6\Service\PostNL\Label\Extractor;

use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Entity\Response\SendShipmentResponse;
use Firstred\PostNL\Exception\NotImplementedException;

class SendShipmentsLabelExtractor implements LabelExtractorInterface
{
    /**
     * @inheritDoc
     */
    public function extract(array $response): array
    {
        // TODO: Implement extract() method.
        throw new NotImplementedException();
    }
}
