<?php

namespace PostNL\Shopware6\Service\PostNL\Label\Extractor;

use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Entity\Response\ResponseShipment;
use PostNL\Shopware6\Service\PostNL\Label\Label;

class GenerateLabelsLabelExtractor implements LabelExtractorInterface
{
    /**
     * @param GenerateLabelResponse[] $response
     * @return Label[]
     */
    public function extract($response): array
    {
        $labels = [];

        foreach ($response as $innerResponse) {
            foreach ($innerResponse->getResponseShipments() as $shipment) {
                foreach ($shipment->getLabels() as $label) {
                    $labels[] = new Label($label->getContent(), $shipment->getBarcode(), $label->getLabeltype());
                }
            }
        }

        return $labels;
    }
}
