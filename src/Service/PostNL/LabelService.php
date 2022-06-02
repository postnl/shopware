<?php

namespace PostNL\Shopware6\Service\PostNL;

use Exception;
use Firstred\PostNL\Util\RFPdi;
use Firstred\PostNL\Util\Util;
use PostNL\Shopware6\Service\PostNL\Label\A6OnA4LandscapeLabelConfiguration;
use PostNL\Shopware6\Service\PostNL\Label\Label;
use setasign\Fpdi\PdfParser\StreamReader;

class LabelService
{
    const ORIENTATION_FORMAT_PORTRAIT = 'P';
    const ORIENTATION_FORMAT_LANDSCAPE = 'L';
    const LABEL_FORMAT_A4 = 'A4';
    const LABEL_FORMAT_A6 = 'A6';
    const A4_DIMENSIONS = [210, 297];
    const A6_DIMENSIONS = [105, 148];

    /**
     * @param Label[] $labels
     * @param A6OnA4LandscapeLabelConfiguration[] $labelConfigurations
     * @param string $mergedFormat The format the labels will be returned as
     * @return string
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function mergeLabels(
        array  $labels,
        array  $labelConfigurations = [],
        string $mergedFormat = self::LABEL_FORMAT_A4

    ): string
    {
        //Sort the labels by size, big ones first
        usort($labels, array($this, 'labelSizeSort'));

        //Start a new pdf
        //Disable header and footer
        $pdf = new RFPdi(self::ORIENTATION_FORMAT_LANDSCAPE, 'mm', $mergedFormat === self::LABEL_FORMAT_A4 ? self::A4_DIMENSIONS : self::A6_DIMENSIONS);

        foreach ($labels as $label) {
            $pdfContent = base64_decode($label->getContent());
            $sizeAndOrientation = Util::getPdfSizeAndOrientation($pdfContent);

            if ($sizeAndOrientation['iso'] == $mergedFormat) {
                //Its 1:1 just add it
                $pdf->addPage($sizeAndOrientation['orientation']);
                //Import the page
                $pdf->setSourceFile(StreamReader::createByString($pdfContent));
                $pdf->useTemplate($pdf->importPage(1));
            } else {
                if ($sizeAndOrientation['iso'] == self::LABEL_FORMAT_A4 && $mergedFormat == self::LABEL_FORMAT_A6) {
                    //No scaling implemented
                    throw new Exception('Destination size is smaller than origin');
                }
                //If a6 on an a4 result, it will be a 2x2
                // Add a config with 4 vacant slots if we do not have any slots left
                if (count($labelConfigurations) == 0) {
                    $labelConfigurations[] = A6OnA4LandscapeLabelConfiguration::createFullLabel();
                }
                $labelConfiguration = $labelConfigurations[0];
                //Start a new page if needed
                if (!$labelConfiguration->active) {
                    $pdf->addPage();
                    $labelConfiguration->active = true;
                }
                //Get a slot
                $freeSlot = $labelConfiguration->getFreeSlot();
                $pdf->setSourceFile(StreamReader::createByString($pdfContent));

                //Import the page for usage
                $importedPage = $pdf->importPage(1);
                //Put a label in it (rotate if needed)
                if ($sizeAndOrientation['orientation'] === self::ORIENTATION_FORMAT_PORTRAIT) {
                    //Rotate the document, paste the loaded pdf and rotate it back
                    $pdf->rotate(90, 0, 0);
                    $coordinates = A6OnA4LandscapeLabelConfiguration::getRotatedCoordinatesXYForSlot($freeSlot);
                    $pdf->useImportedPage($importedPage, $coordinates[0], $coordinates[1]);
                    //Set the page back for un-rotated imports
                    $pdf->rotate(0, 0, 0);
                } else {
                    $coordinates = A6OnA4LandscapeLabelConfiguration::getCoordinatesXYForSlot($freeSlot);
                    $pdf->useImportedPage($importedPage, $coordinates[0], $coordinates[1]);
                }

                //Mark slot as filled
                $labelConfiguration->fillSlot($freeSlot);
                //Discard if full
                if (!$labelConfiguration->hasFreeSlots()) {
                    array_shift($labelConfigurations);
                }
            }
        }

        return base64_encode($pdf->output('', 'S'));
    }

    private function labelSizeSort($a, $b)
    {
        $aSize = Util::getPdfSizeAndOrientation($a);
        $bSize = Util::getPdfSizeAndOrientation($b);
        if ($aSize === $bSize) {
            return 0;
        }
        if ($aSize == 'A6' && $bSize == 'A4') {
            return 1;
        }
        if ($aSize == 'A4' && $bSize == 'A6') {
            return -1;
        }
    }
}
