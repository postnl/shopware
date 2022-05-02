<?php

namespace PostNL\Shopware6\Service\PostNL;

use Firstred\PostNL\Entity\Label;
use Firstred\PostNL\Entity\Response\GenerateLabelResponse;
use Firstred\PostNL\Entity\Response\ResponseShipment;
use Firstred\PostNL\Entity\Shipment;
use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shopware6\Service\PostNL\Builder\ShipmentBuilder;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use PostNL\Shopware6\Service\PostNL\Label\A6OnA4LandscapeLabelConfiguration;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shopware6\Service\Shopware\OrderService;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

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

    public function __construct(
        ApiFactory         $apiFactory,
        OrderDataExtractor $orderDataExtractor,
        OrderService       $orderService,
        ConfigService      $configService,
        LabelService       $labelService,
        ShipmentBuilder    $shipmentBuilder
    )
    {
        $this->apiFactory = $apiFactory;
        $this->orderDataExtractor = $orderDataExtractor;
        $this->orderService = $orderService;
        $this->configService = $configService;
        $this->labelService = $labelService;
        $this->shipmentBuilder = $shipmentBuilder;
    }

    /**
     * @param OrderCollection $orders
     * @param Context $context
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
    public function shipOrders(OrderCollection $orders, bool $confirm, Context $context)
    {
        $response = [];

        $config = $this->configService->getConfiguration(null, $context);

        $format = $config->getPrinterFormat() === 'a4' ? Label::FORMAT_A4 : Label::FORMAT_A6;
        $a6Orientation = 'P';

        $printerType = 'GraphicFile|PDF';

        $positions = [
            1 => true,
            2 => true,
            3 => true,
            4 => true,
        ];

        // Yes, this should be getSalesChannelIds.
        foreach (array_unique(array_values($orders->getSalesChannelIs())) as $salesChannelId) {
            $apiClient = $this->apiFactory->createClientForSalesChannel($salesChannelId, $context);

            $salesChannelOrders = $orders->filterBySalesChannelId($salesChannelId);

            $shipments = [];
            foreach ($salesChannelOrders as $order) {
                $shipments[] = $this->shipmentBuilder->buildShipment($order, $context);
            }
            
            /** @var GenerateLabelResponse[] $labelResponse */
            $labelResponses = $apiClient->generateLabels(
                $shipments,
                $printerType,
                $confirm,
                false,
                $format,
                $positions,
                $a6Orientation
            );

            foreach ($labelResponses as $labelResponse) {
                $response[] = $labelResponse;
            }

            foreach ($salesChannelOrders as $order) {
                if($confirm) {
                    $this->orderService->updateOrderCustomFields($order->getId(), ['confirm' => $confirm], $context);
                }
            }
        }

        if ($printerType !== 'GraphicFile|PDF') {
            return $response;
        }

        //Extract labels
        $labels = [];

        /** @var GenerateLabelResponse $labelResponses */
        foreach ($labelResponses as $labelResponse) {
            foreach ($labelResponse->getResponseShipments()[0]->getLabels() as $label) {
                $labels[] = $label;
            }
        }
        $format = $config->getPrinterFormat() === 'a4' ? LabelService::LABEL_FORMAT_A4 : LabelService::LABEL_FORMAT_A6;
        return $this->labelService->mergeLabels($labels,[],$format);
    }
}
