<?php

namespace PostNL\Shopware6\Service\PostNL;

use Firstred\PostNL\Entity\Label;
use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Exception\PostNLException;
use Firstred\PostNL\PostNL;
use Firstred\PostNL\Util\RFPdi;
use Firstred\PostNL\Util\Util;
use setasign\Fpdi\PdfParser\StreamReader;

class LabelService
{
    /**
     * @param GenerateLabelResponse[] $responseShipments
     * @param int $format
     * @param array<int, bool> $positions
     * @param string $a6Orientation
     * @return string
     * @throws PostNLException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function mergeLabels(
        array  $responseShipments,
        int    $format = Label::FORMAT_A4,
        array  $positions = [
            1 => true,
            2 => true,
            3 => true,
            4 => true,
        ],
        string $a6Orientation = 'P'
    )
    {
        // Disable header and footer
        $pdf = new RFPdi('P', 'mm', Label::FORMAT_A4 === $format ? [210, 297] : [105, 148]);
        $deferred = [];
        $firstPage = true;
        if (Label::FORMAT_A6 === $format) {
            foreach ($responseShipments as $responseShipment) {
                foreach ($responseShipment->getResponseShipments()[0]->getLabels() as $label) {
                    $pdfContent = base64_decode($label->getContent());
                    $sizes = Util::getPdfSizeAndOrientation($pdfContent);
                    if ('A6' === $sizes['iso']) {
                        $pdf->addPage($a6Orientation);
                        $correction = [0, 0];
                        if ('L' === $a6Orientation && 'P' === $sizes['orientation']) {
                            $correction[0] = -84;
                            $correction[1] = -0.5;
                            $pdf->rotateCounterClockWise();
                        } elseif ('P' === $a6Orientation && 'L' === $sizes['orientation']) {
                            $correction[0] = -128;
                            $correction[1] = -0.5;
                            $pdf->rotateCounterClockWise();
                        }
                        $pdf->setSourceFile(StreamReader::createByString($pdfContent));
                        $pdf->useTemplate($pdf->importPage(1), $correction[0], $correction[1]);
                    } else {
                        // Assuming A4 here (could be multi-page) - defer to end
                        $stream = StreamReader::createByString($pdfContent);
                        $deferred[] = ['stream' => $stream, 'sizes' => $sizes];
                    }
                }
            }
        } else {
            $a6s = 4; // Amount of A6s available
            foreach ($responseShipments as $responseShipment) {
                if ($responseShipment instanceof PostNLException) {
                    throw $responseShipment;
                }
                $pdfContent = base64_decode($responseShipment->getResponseShipments()[0]->getLabels()[0]->getContent());
                $sizes = Util::getPdfSizeAndOrientation($pdfContent);

                if ('A6' === $sizes['iso']) {
                    foreach($responseShipment->getResponseShipments()[0]->getLabels() as $label) {
                        $pdfContent = base64_decode($label->getContent());

                        if ($firstPage) {
                            $pdf->addPage('P', [297, 210], 90);
                        }
                        $firstPage = false;
                        while (empty($positions[5 - $a6s]) && $a6s >= 1) {
                            $positions[5 - $a6s] = true;
                            --$a6s;
                        }
                        if ($a6s < 1) {
                            $pdf->addPage('P', [297, 210], 90);
                            $a6s = 4;
                        }
                        $pdf->rotateCounterClockWise();
                        $pdf->setSourceFile(StreamReader::createByString($pdfContent));
                        $pdf->useTemplate($pdf->importPage(1), PostNL::$a6positions[$a6s][0], PostNL::$a6positions[$a6s][1]);
                        --$a6s;
                        if ($a6s < 1) {
                            if ($responseShipment !== end($responseShipments)) {
                                $pdf->addPage('P', [297, 210], 90);
                            }
                            $a6s = 4;
                        }
                    }
                } else {
                    // Assuming A4 here (could be multi-page) - defer to end
                    if (count($responseShipment->getResponseShipments()[0]->getLabels()) > 1) {
                        $stream = [];
                        foreach ($responseShipment->getResponseShipments()[0]->getLabels() as $labelContent) {
                            $stream[] = StreamReader::createByString(base64_decode($labelContent->getContent()));
                        }
                        $deferred[] = ['stream' => $stream, 'sizes' => $sizes];
                    } else {
                        $stream = StreamReader::createByString(base64_decode($pdfContent));
                        $deferred[] = ['stream' => $stream, 'sizes' => $sizes];
                    }
                }
            }
        }
        foreach ($deferred as $defer) {
            $sizes = $defer['sizes'];
            $pdf->addPage($sizes['orientation'], 'A4');
            $pdf->rotateCounterClockWise();
            if (is_array($defer['stream']) && count($defer['stream']) > 1) {
                // Multilabel
                if (2 === count($deferred['stream'])) {
                    $pdf->setSourceFile($defer['stream'][0]);
                    $pdf->useTemplate($pdf->importPage(1), -190, 0);
                    $pdf->setSourceFile($defer['stream'][1]);
                    $pdf->useTemplate($pdf->importPage(1), -190, 148);
                } else {
                    $pdf->setSourceFile($defer['stream'][0]);
                    $pdf->useTemplate($pdf->importPage(1), -190, 0);
                    $pdf->setSourceFile($defer['stream'][1]);
                    $pdf->useTemplate($pdf->importPage(1), -190, 148);
                    for ($i = 2; $i < count($defer['stream']); ++$i) {
                        $pages = $pdf->setSourceFile($defer['stream'][$i]);
                        for ($j = 1; $j < $pages + 1; ++$j) {
                            $pdf->addPage($sizes['orientation'], 'A4');
                            $pdf->rotateCounterClockWise();
                            $pdf->useTemplate($pdf->importPage(1), -190, 0);
                        }
                    }
                }
            } else {
                if (is_resource($defer['stream']) || $defer['stream'] instanceof StreamReader) {
                    $pdf->setSourceFile($defer['stream']);
                } else {
                    $pdf->setSourceFile($defer['stream'][0]);
                }
                $pdf->useTemplate($pdf->importPage(1), -190, 0);
            }
        }

        return base64_encode($pdf->output('', 'S'));
    }

}
