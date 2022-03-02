<?php declare(strict_types=1);

namespace PostNL\Shipments\Controller\Api;

use PostNL\Shipments\Facade\ShipmentFacade;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\QueryDataBag;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @param Context $context
     * @return JsonResponse
     */
    public function barcodes(Request $request, Context $context): JsonResponse
    {
        $orderIds = $request->get('orderIds');

        return $this->getBarcodesResponse($orderIds, $context);
    }

    /**
     * @Route("/api/v{version}/_action/postnl/shipment/barcodes",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.shipment.barcodes.legacy", methods={"GET"})
     *
     * @param Request $request
     * @param Context $context
     * @return JsonResponse
     */
    public function barcodesLegacy(Request $request, Context $context): JsonResponse
    {
        $orderIds = $request->get('orderIds');

        return $this->getBarcodesResponse($orderIds, $context);
    }

    /**
     * @Route("/api/_action/postnl/shipment/labels",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.shipment.labels", methods={"GET"})
     *
     * @param QueryDataBag $data
     * @param Context $context
     * @return Response
     */
    public function labels(QueryDataBag $data, Context $context): Response
    {
        $orderIds = $data->get('orderIds')->all();
        $overrideProduct = $data->getBoolean('overrideProduct');
        $overrideProductId = $data->get('overrideProductId');

        return $this->getLabelsResponse($orderIds, $overrideProduct, $overrideProductId, $context);
    }

    /**
     * @Route("/api/v{version}/_action/postnl/shipment/labels",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.shipment.labels.legacy", methods={"GET"})
     *
     * @param QueryDataBag $data
     * @param Context $context
     * @return Response
     */
    public function labelsLegacy(QueryDataBag $data, Context $context): Response
    {
        $orderIds = $data->get('orderIds')->all();
        $overrideProduct = $data->getBoolean('overrideProduct');
        $overrideProductId = $data->get('overrideProductId');

        return $this->getLabelsResponse($orderIds, $overrideProduct, $overrideProductId, $context);
    }

    /**
     * @param string $apiKey
     * @param bool $sandbox
     * @return JsonResponse
     */
    private function getBarcodesResponse(array $orderIds, Context $context): JsonResponse
    {
        $generatedBarCodes = $this->shipmentFacade->generateBarcodes($orderIds, $context);

        $this->logger->info("Generated barcodes", [
            'generatedBarCodes' => $generatedBarCodes,
        ]);

        return $this->json(['barcodes' => $generatedBarCodes]);
    }

    /**
     * @param array $orderIds
     * @param bool $overrideProduct
     * @param string $overrideProductId
     * @param Context $context
     * @return Response
     */
    private function getLabelsResponse(
        array $orderIds,
        bool $overrideProduct,
        string $overrideProductId,
        Context $context
    ): Response
    {
        $pdf = $this->shipmentFacade->shipOrders(
            $orderIds,
            $overrideProduct,
            $overrideProductId,
            $context
        );

        return $this->createBinaryResponse(
            'PostNL_Labels_' . date('YmdHis') . '.pdf',
            base64_decode($pdf),
            true,
            'application/pdf'
        );
    }

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
