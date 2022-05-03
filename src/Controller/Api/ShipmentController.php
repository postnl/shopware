<?php declare(strict_types=1);

namespace PostNL\Shopware6\Controller\Api;

use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shopware6\Facade\ShipmentFacade;
use PostNL\Shopware6\Service\PostNL\Label\PrinterFileType;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\QueryDataBag;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class ShipmentController extends AbstractController
{
    /**
     * @var ShipmentFacade
     */
    protected $shipmentFacade;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ShipmentFacade  $shipmentFacade,
        LoggerInterface $logger
    )
    {
        $this->shipmentFacade = $shipmentFacade;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/_action/postnl/shipment/barcodes",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.shipment.barcodes", methods={"GET"})
     *
     * @param QueryDataBag $data
     * @param Context $context
     * @return JsonResponse
     * @throws \Firstred\PostNL\Exception\PostNLException
     */
    public function barcodes(QueryDataBag $data, Context $context): JsonResponse
    {
        $orderIds = $data->get('orderIds', new QueryDataBag())->all();

        try {
            $generatedBarCodes = $this->shipmentFacade->generateBarcodes($orderIds, $context);

            $this->logger->info("Generated barcodes", [
                'generatedBarCodes' => $generatedBarCodes,
            ]);

            return $this->json(['barcodes' => $generatedBarCodes]);
        } catch (PostNLException $e) {
            $this->logger->error($e->getMessage());

            return $this->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @Route("/api/_action/postnl/shipment/zones",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.shipment.zones", methods={"GET"})
     *
     * @param QueryDataBag $data
     * @param Context $context
     * @return JsonResponse
     */
    public function determineZones(QueryDataBag $data, Context $context): JsonResponse
    {
        $orderIds = $data->get('orderIds', new QueryDataBag())->all();

        $zones = $this->shipmentFacade->determineZones($orderIds, $context);

        return $this->json([
            'zones' => $zones
        ]);
    }

    /**
     * @Route("/api/_action/postnl/shipment/change",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.shipment.change", methods={"POST"})
     *
     * @param RequestDataBag $data
     * @param Context $context
     * @return JsonResponse
     */
    public function changeProduct(RequestDataBag $data, Context $context): JsonResponse
    {
        $orderIds = $data->get('orderIds', new QueryDataBag())->all();
        $productId = $data->get('productId');

        $this->shipmentFacade->changeProduct($orderIds, $productId, $context);

        return $this->json(null, 204);
    }

    /**
     * @Route("/api/_action/postnl/shipment/create",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.shipment.create", methods={"GET"})
     *
     * @param QueryDataBag $data
     * @param Context $context
     * @return Response
     */
    public function create(QueryDataBag $data, Context $context): Response
    {
        $orderIds = $data->get('orderIds', new QueryDataBag())->all();
        $confirmShipments = $data->getBoolean('confirmShipments');
        $downloadLabels = $data->getBoolean('downloadLabels');

        $response = $this->shipmentFacade->shipOrders(
            $orderIds,
            $confirmShipments,
            $context
        );

        if (!$downloadLabels) {
            return $this->json(null, 204);
        }

        $contentType = '';
        $fileExtension = '';

        switch ($response->getType()) {
            case PrinterFileType::PDF:
                $contentType = 'application/pdf';
                $fileExtension = '.pdf';
                break;
            case PrinterFileType::JPG:
            case PrinterFileType::GIF:
                $contentType = 'application/zip';
                $fileExtension = '.zip';
                break;
            case PrinterFileType::ZPL:
                $contentType = 'text/zpl';
                $fileExtension = '.zpl';
                break;
        }

        return $this->createBinaryResponse(
            'PostNL_Labels_' . date('YmdHis') . $fileExtension,
            base64_decode($response->getContent()),
            true,
            $contentType
        );
    }

    /**
     * @param string $filename
     * @param string $content
     * @param bool $forceDownload
     * @param string $contentType
     * @return Response
     */
    private function createBinaryResponse(string $filename, string $content, bool $forceDownload, string $contentType): Response
    {
        $response = new Response($content);

        $disposition = HeaderUtils::makeDisposition(
            $forceDownload ? HeaderUtils::DISPOSITION_ATTACHMENT : HeaderUtils::DISPOSITION_INLINE,
            $filename,
            // only printable ascii
            preg_replace('/[\x00-\x1F\x7F-\xFF]/', '_', $filename)
        );

        $response->headers->set('Content-Type', $contentType);
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
