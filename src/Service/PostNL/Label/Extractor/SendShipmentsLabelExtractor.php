<?php

namespace PostNL\Shopware6\Service\PostNL\Label\Extractor;

use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Entity\Response\SendShipmentResponse;
use Firstred\PostNL\Exception\NotImplementedException;
use PostNL\Shopware6\Service\PostNL\Label\Label;

class SendShipmentsLabelExtractor implements LabelExtractorInterface
{
    /**
     * @param SendShipmentResponse $response
     * @return Label[]
     */
    public function extract($response): array
    {
        $labels = [];

        foreach($response->getResponseShipments() as $shipment) {
            foreach ($shipment->getLabels() as $label) {
                $labels[] = new Label($label->getContent(), $shipment->getBarcode(), $label->getLabeltype());
            }
        }

        return $labels;
    }
}
