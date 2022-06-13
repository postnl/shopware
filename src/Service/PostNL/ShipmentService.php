<?php

namespace PostNL\Shopware6\Service\PostNL;


use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Exception\PostNLException;
use Firstred\PostNL\PostNL;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\PostNL\Builder\ShipmentBuilder;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\PostNL\Label\Extractor\LabelExtractorInterface;
use PostNL\Shopware6\Service\PostNL\Label\Label;
use PostNL\Shopware6\Service\PostNL\Label\MergedLabelResponse;
use PostNL\Shopware6\Service\PostNL\Product\ProductService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shopware6\Service\Shopware\OrderService;
use PostNL\Shopware6\Struct\Attribute\OrderAttributeStruct;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use ZipArchive;

class ShipmentService
{
    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    /**
     * @var OrderDataExtractor
     */
    protected $orderDataExtractor;

    /**
     * @var OrderService
     */
    protected $orderService;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var LabelService
     */
    protected $labelService;

    /**
     * @var ShipmentBuilder
     */
    protected $shipmentBuilder;

    /**
     * @var LabelExtractorInterface
     */
    protected $labelExtractor;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var ProductService
     */
    protected $productService;

    /**
     * @param ApiFactory              $apiFactory
     * @param OrderDataExtractor      $orderDataExtractor
     * @param OrderService            $orderService
     * @param ConfigService           $configService
     * @param LabelService            $labelService
     * @param ShipmentBuilder         $shipmentBuilder
     * @param LabelExtractorInterface $labelExtractor
     * @param AttributeFactory        $attributeFactory
     * @param ProductService          $productService
     */
    public function __construct(ApiFactory $apiFactory, OrderDataExtractor $orderDataExtractor, OrderService $orderService, ConfigService $configService, LabelService $labelService, ShipmentBuilder $shipmentBuilder, LabelExtractorInterface $labelExtractor, AttributeFactory $attributeFactory, ProductService $productService)
    {
        $this->apiFactory = $apiFactory;
        $this->orderDataExtractor = $orderDataExtractor;
        $this->orderService = $orderService;
        $this->configService = $configService;
        $this->labelService = $labelService;
        $this->shipmentBuilder = $shipmentBuilder;
        $this->labelExtractor = $labelExtractor;
        $this->attributeFactory = $attributeFactory;
        $this->productService = $productService;
    }


    /**
     * @param OrderCollection $orders
     * @param Context         $context
     * @return array<string, string>
     * @throws PostNLException
     * @throws \Firstred\PostNL\Exception\CifDownException
     * @throws \Firstred\PostNL\Exception\CifException
     * @throws \Firstred\PostNL\Exception\HttpClientException
     * @throws \Firstred\PostNL\Exception\InvalidBarcodeException
     * @throws \Firstred\PostNL\Exception\InvalidConfigurationException
     * @throws \Firstred\PostNL\Exception\ResponseException
     */
    public function generateBarcodesForOrders(OrderCollection $orders, Context $context): array
    {
        $barCodesAssigned = [];

        // Yes, this should be getSalesChannelIds.
        foreach (array_unique(array_values($orders->getSalesChannelIs())) as $salesChannelId) {
            $apiClient = $this->apiFactory->createClientForSalesChannel($salesChannelId, $context);

            $salesChannelOrders = $orders->filterBySalesChannelId($salesChannelId);

            $isoCodes = $salesChannelOrders->map(function (OrderEntity $order) {
                return $this->orderDataExtractor->extractDeliveryCountry($order)->getIso();
            });

            $barCodes = $apiClient->generateBarcodesByCountryCodes(array_count_values($isoCodes));

            foreach ($salesChannelOrders as $order) {
                $iso = $this->orderDataExtractor->extractDeliveryCountry($order)->getIso();
                $barCode = array_pop($barCodes[$iso]);

                $barCodesAssigned[$order->getId()] = $barCode;

                $this->orderService->updateOrderCustomFields($order->getId(), ['barCode' => $barCode], $context);
            }
        }

        return $barCodesAssigned;
    }

    /**
     * @throws \Firstred\PostNL\Exception\HttpClientException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Firstred\PostNL\Exception\ResponseException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws PostNLException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \Firstred\PostNL\Exception\NotSupportedException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     * @throws \Firstred\PostNL\Exception\InvalidArgumentException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     */
    public function shipOrders(OrderCollection $orders, bool $confirm, Context $context): MergedLabelResponse
    {
        $response = [];

        $config = $this->configService->getConfiguration(null, $context);

        /* Does not work yet, isn't needed yet, and when it is it should be moved to the foreach
        $printerType = PrinterFileType::getPrefixForConfigFiletype($config->getPrinterFile());
        if ($printerType != PrinterFileType::PDFPrefix) {
            $printerType .= " " . $config->getPrinterDPI();
        }
        */

        $printerType = 'GraphicFile|PDF';

        /** @var Label[] $labels */
        $labels = [];

        // Yes, this should be getSalesChannelIds.
        foreach (array_unique(array_values($orders->getSalesChannelIs())) as $salesChannelId) {
            $apiClient = $this->apiFactory->createClientForSalesChannel($salesChannelId, $context);

            $salesChannelOrders = $orders->filterBySalesChannelId($salesChannelId);
            //Separate the order if mailbox or not
            $mailBoxOrders = [];
            $nonMailBoxOrders = [];

            foreach ($salesChannelOrders as $salesChannelOrder) {
                /** @var OrderAttributeStruct $orderAttributes */
                $orderAttributes = $this->attributeFactory->createFromEntity($salesChannelOrder, $context);
                $product = $this->productService->getProduct($orderAttributes->getProductId(), $context);

                if ($product->getDeliveryType() === DeliveryType::MAILBOX) {
                    $mailBoxOrders[] = $salesChannelOrder;
                } else {
                    $nonMailBoxOrders[] = $salesChannelOrder;
                }
            }

            //Create the mailbox labels that always need to be confirmed
            if (!empty($mailBoxOrders)) {
                $labels = array_merge($labels, $this->createLabelsForOrders(
                    new OrderCollection($mailBoxOrders),
                    $apiClient,
                    $printerType,
                    true,
                    $context
                ));
            }
            //Create the other labels confirmed on preference
            if (!empty($nonMailBoxOrders)) {
                $labels = array_merge($labels, $this->createLabelsForOrders(
                    new OrderCollection($nonMailBoxOrders),
                    $apiClient,
                    $printerType,
                    $confirm,
                    $context
                ));
            }
        }

        switch ($config->getPrinterFile()) {
            default:
            case 'pdf':
                //Merge to into one document
                $format = $config->getPrinterFormat() === 'a4' ? LabelService::LABEL_FORMAT_A4 : LabelService::LABEL_FORMAT_A6;
                return new MergedLabelResponse('pdf', $this->labelService->mergeLabels($labels, [], $format));
            case 'gif':
                //Merge into one zip
                if (count($labels) == 1) {
                    return new MergedLabelResponse('gif', $labels[0]->getContent());
                } else {
                    return $this->zipImages($labels, 'gif');
                }
            case 'jpg':
                //Merge into one zip
                if (count($labels) == 1) {
                    return new MergedLabelResponse('jpg', $labels[0]->getContent());
                } else {
                    return $this->zipImages($labels, 'jpg');
                }
            case 'zpl':
                //Merge into one string
                $mergedLabel = '';
                foreach ($labels as $label) {
                    $mergedLabel .= " " . base64_decode($label->getContent());
                }
                return new MergedLabelResponse('zpl', base64_encode($mergedLabel));
        }

    }

    /**
     * @param OrderCollection $orders
     * @param PostNL          $apiClient
     * @param string          $printerType
     * @param bool            $confirm
     * @param Context         $context
     * @return Label[]
     * @throws PostNLException
     * @throws \Firstred\PostNL\Exception\HttpClientException
     * @throws \Firstred\PostNL\Exception\InvalidArgumentException
     * @throws \Firstred\PostNL\Exception\NotSupportedException
     * @throws \Firstred\PostNL\Exception\ResponseException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    private function createLabelsForOrders(
        OrderCollection $orders,
        PostNL          $apiClient,
        string          $printerType,
        bool            $confirm,
        Context         $context
    ): array
    {
        $shipments = [];
        foreach ($orders as $order) {
            $shipments[] = $this->shipmentBuilder->buildShipment($order, $context);
        }

        /** @var GenerateLabelResponse[] $labelResponses */
        $labelResponses = $apiClient->generateLabels(
            $shipments,
            $printerType,
            $confirm
        );

        if ($confirm) {
        foreach ($orders as $order) {
                $this->orderService->updateOrderCustomFields($order->getId(), ['confirm' => $confirm], $context);
            }
        }

        return $this->labelExtractor->extract($labelResponses);
    }

    private function zipImages(array $labels, string $extension): MergedLabelResponse
    {
        $zip = new ZipArchive();
        $filePath = 'PostNL_Labels_' . date('YmdHis');
        $zip->open($filePath, ZipArchive::CREATE);
        foreach ($labels as $label) {
            $zip->addFromString($label->getBarcode() .
                "_" .
                str_replace(' ', '_', $label->getType()) .
                "." .
                $extension, base64_decode($label->getContent()));
        }
        $zip->close();
        return new MergedLabelResponse('zip', base64_encode(file_get_contents($filePath)));
    }
}
