<?php

namespace PostNL\Shopware6\Service\PostNL;

use Exception;
use Firstred\PostNL\Util\RFPdi;
use Firstred\PostNL\Util\Util;
use PostNL\Shopware6\Service\PostNL\Label\A6OnA4LandscapeLabelConfiguration;
use PostNL\Shopware6\Service\PostNL\Label\Label;
use PostNL\Shopware6\Service\PostNL\Label\LabelDefaults;
use setasign\Fpdi\PdfParser\StreamReader;

class LabelService
{
    /**
     * @param Label[]                             $labels
     * @param A6OnA4LandscapeLabelConfiguration[] $labelConfigurations
     * @param string                              $mergedFormat The format the labels will be returned as
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
        string $mergedFormat = LabelDefaults::LABEL_FORMAT_A4

    ): string
    {
        //Sort the labels by size, big ones first
        usort($labels, [$this, 'labelSizeSort']);

        //Start a new pdf
        //Disable header and footer
        $pdf = new RFPdi(LabelDefaults::ORIENTATION_FORMAT_LANDSCAPE, 'mm', $mergedFormat === LabelDefaults::LABEL_FORMAT_A4 ? LabelDefaults::A4_DIMENSIONS : LabelDefaults::A6_DIMENSIONS);

        foreach ($labels as $label) {
            $pdfContent = base64_decode($label->getContent());
            $sizeAndOrientation = Util::getPdfSizeAndOrientation($pdfContent);
            $labelFormat = $sizeAndOrientation['iso'];
            $labelOrientation = $sizeAndOrientation['orientation'];

            if ($labelFormat == $mergedFormat) {
                //Its 1:1 just add it
                $pdf->addPage($labelOrientation);
                //Import the page
                $pdf->setSourceFile(StreamReader::createByString($pdfContent));
                $pdf->useTemplate($pdf->importPage(1));
            } else {

                if ($labelFormat == LabelDefaults::LABEL_FORMAT_A4 && $mergedFormat == LabelDefaults::LABEL_FORMAT_A6) {
                    //No scaling implemented
                    throw new Exception('Destination size is smaller than origin');
                }

                if ($labelFormat == LabelDefaults::LABEL_FORMAT_A5 && $mergedFormat == LabelDefaults::LABEL_FORMAT_A6) {
                    //No scaling implemented
                    throw new Exception('Destination size is smaller than origin');
                }

                //Discard if unusable
                if (!empty($labelConfiguration) && !$labelConfiguration->hasFreeSlots($labelFormat)) {
                    array_shift($labelConfigurations);
                }

                //If a6 on an a4 result, it will be a 2x2
                // Add a config with 4 vacant slots if we do not have any slots left
                if (count($labelConfigurations) == 0) {
                    $labelConfigurations[] = A6OnA4LandscapeLabelConfiguration::createFullLabel();
                }
                //Get a labelconfig with room for the current format
                $labelConfiguration = $labelConfigurations[0];
                //Start a new page if needed
                if (!$labelConfiguration->active) {
                    $pdf->addPage();
                    $labelConfiguration->active = true;
                }
                //Get a slot
                $freeSlot = $labelConfiguration->getFreeSlot($labelFormat);

                //Load the label page
                $pdf->setSourceFile(StreamReader::createByString($pdfContent));

                //Import the page for usage
                $importedPage = $pdf->importPage(1);
                //Put a label in it (rotate if needed) (A5 is oriented differently)
                if ($labelOrientation === LabelDefaults::ORIENTATION_FORMAT_PORTRAIT) {
                    //Rotate the document, paste the loaded pdf and rotate it back
                    if ($labelFormat == LabelDefaults::LABEL_FORMAT_A5) {
                        $coordinates = A6OnA4LandscapeLabelConfiguration::getCoordinatesXYForSlot($freeSlot, $labelFormat);
                        $pdf->useImportedPage($importedPage, $coordinates[0], $coordinates[1]);
                    } else {
                        $pdf->rotate(90, 0, 0);
                        $coordinates = A6OnA4LandscapeLabelConfiguration::getRotatedCoordinatesXYForSlot($freeSlot, $labelFormat);
                        $pdf->useImportedPage($importedPage, $coordinates[0], $coordinates[1]);
                        //Set the page back for un-rotated imports
                        $pdf->rotate(0, 0, 0);
                    }


                } else {
                    if ($labelFormat == LabelDefaults::LABEL_FORMAT_A5) {
                        $pdf->rotate(90, 0, 0);
                        $coordinates = A6OnA4LandscapeLabelConfiguration::getRotatedCoordinatesXYForSlot($freeSlot, $labelFormat);
                        $pdf->useImportedPage($importedPage, $coordinates[0], $coordinates[1]);
                        //Set the page back for un-rotated imports
                        $pdf->rotate(0, 0, 0);
                    } else {
                        $coordinates = A6OnA4LandscapeLabelConfiguration::getCoordinatesXYForSlot($freeSlot, $labelFormat);
                        $pdf->useImportedPage($importedPage, $coordinates[0], $coordinates[1]);
                    }
                }

                //Mark slot as filled
                $labelConfiguration->fillSlot($freeSlot, $labelFormat);

            }
        }

        return base64_encode($pdf->output('', 'S'));
    }

    /**
     * @param $a Label
     * @param $b Label
     * @return int|void
     */
    private function labelSizeSort(Label $a, Label $b)
    {
        $aSize = Util::getPdfSizeAndOrientation(base64_decode($a->getContent()))['iso'];
        $bSize = Util::getPdfSizeAndOrientation(base64_decode($b->getContent()))['iso'];

        if ($aSize === $bSize) {
            return 0;
        }

        switch ($aSize) {
            case LabelDefaults::LABEL_FORMAT_A6:
                if ($bSize == LabelDefaults::LABEL_FORMAT_A4) {
                    return 1;
                }
                if ($bSize == LabelDefaults::LABEL_FORMAT_A5) {
                    return 1;
                }
                break;
            case LabelDefaults::LABEL_FORMAT_A5:
                if ($bSize == LabelDefaults::LABEL_FORMAT_A4) {
                    return 1;
                }
                if ($bSize === LabelDefaults::LABEL_FORMAT_A6) {
                    return -1;
                }
                break;
            case LabelDefaults::LABEL_FORMAT_A4:
                if ($bSize == LabelDefaults::LABEL_FORMAT_A5) {
                    return -1;
                }
                if ($bSize == LabelDefaults::LABEL_FORMAT_A6) {
                    return -1;
                }
                break;
        }
    }
}
