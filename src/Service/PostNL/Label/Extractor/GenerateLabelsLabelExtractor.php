<?php

namespace PostNL\Shopware6\Service\PostNL\Label\Extractor;

use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Entity\Response\ResponseShipment;
use PostNL\Shopware6\Service\PostNL\Label\Label;

class GenerateLabelsLabelExtractor implements LabelExtractorInterface
{
    /**
     * @inheritDoc
     */
    public function extract(array $responses): array
    {
        $labels = [];

        /** @var GenerateLabelResponse[] $response */
        foreach ($responses as $response) {
            /** @var ResponseShipment $shipment */
            foreach ($response->getResponseShipments() as $shipment) {
                foreach ($shipment->getLabels() as $label) {
                    $labels[] = new Label($label->getContent(), $shipment->getBarcode(), $label->getLabeltype());
                }
            }
        }

        return $labels;
    }
}
