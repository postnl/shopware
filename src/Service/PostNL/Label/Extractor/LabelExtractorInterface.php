<?php

namespace PostNL\Shopware6\Service\PostNL\Label\Extractor;

use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Entity\Response\SendShipmentResponse;
use PostNL\Shopware6\Service\PostNL\Label\Label;

interface LabelExtractorInterface
{
    /**
     * @param GenerateLabelResponse[]|SendShipmentResponse[] $response
     * @return Label[]
     */
    public function extract(array $response): array;
}
