<?php declare(strict_types=1);

namespace PostNL\Shopware6\Controller\Api;

use PostNL\Shopware6\Facade\ShipmentFacade;
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

        $generatedBarCodes = $this->shipmentFacade->generateBarcodes($orderIds, $context);

        $this->logger->info("Generated barcodes", [
            'generatedBarCodes' => $generatedBarCodes,
        ]);

        return $this->json(['barcodes' => $generatedBarCodes]);
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
        $confirmShipments = $data->getBoolean('confirmShipments');

        $pdf = $this->shipmentFacade->shipOrders(
            $orderIds,
            $overrideProduct,
            $overrideProductId,
            $confirmShipments,
            $context
        );

        return $this->createBinaryResponse(
            'PostNL_Labels_' . date('YmdHis') . '.pdf',
            base64_decode($pdf),
            true,
            'application/pdf'
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
