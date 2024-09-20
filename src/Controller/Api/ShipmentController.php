<?php declare(strict_types=1);

namespace PostNL\Shopware6\Controller\Api;

use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shopware6\Facade\ShipmentFacade;
use PostNL\Shopware6\Struct\Attribute\OrderReturnAttributeStruct;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Validation\DataBag\QueryDataBag;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(defaults: ['_routeScope' => ['api']])]
class ShipmentController extends AbstractController
{
    protected ShipmentFacade $shipmentFacade;

    protected LoggerInterface $logger;

    public function __construct(
        ShipmentFacade  $shipmentFacade,
        LoggerInterface $logger
    )
    {
        $this->shipmentFacade = $shipmentFacade;
        $this->logger = $logger;
    }

    #[Route(path: '/api/_action/postnl/shipment/barcodes', name: 'api.action.postnl.shipment.barcodes', defaults: ['auth_enabled' => true], methods: ['GET'])]
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
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route(path: '/api/_action/postnl/shipment/zones', name: 'api.action.postnl.shipment.zones', defaults: ['auth_enabled' => true], methods: ['GET'])]
    public function determineZones(QueryDataBag $data, Context $context): JsonResponse
    {
        $orderIds = $data->get('orderIds', new QueryDataBag())->all();

        $sourceZones = $this->shipmentFacade->determineSourceZones($orderIds, $context);
        $destinationZones = $this->shipmentFacade->determineDestinationZones($orderIds, $context);

        return $this->json([
            'source' => $sourceZones,
            'destination' => $destinationZones,
        ]);
    }

    #[Route(path: '/api/_action/postnl/shipment/change', name: 'api.action.postnl.shipment.change', defaults: ['auth_enabled' => true], methods: ['POST'])]
    public function changeProduct(RequestDataBag $data, Context $context): JsonResponse
    {
        $orderIds = $data->get('orderIds', new QueryDataBag())->all();
        $productId = $data->get('productId');

        $this->shipmentFacade->changeProduct($orderIds, $productId, $context);

        return $this->json(null, 204);
    }

    #[Route(path: '/api/_action/postnl/shipment/create', name: 'api.action.postnl.shipment.create', defaults: ['auth_enabled' => true], methods: ['GET'])]
    public function create(QueryDataBag $data, Context $context): Response
    {
        $orderIds = $data->get('orderIds', new QueryDataBag())->all();
        $confirmShipments = $data->getBoolean('confirmShipments');
        $downloadLabels = $data->getBoolean('downloadLabels');
        $smartReturn = $data->getBoolean('smartReturn');

        if($smartReturn) {
            $context->addState(OrderReturnAttributeStruct::S_SMART_RETURN);
        }

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
            case 'pdf':
                $contentType = 'application/pdf';
                $fileExtension = '.pdf';
                break;
            case 'gif':
                $contentType = 'image/gif';
                $fileExtension = '.gif';
                break;
            case 'jpg':
                $contentType = 'image/jpg';
                $fileExtension = '.jpg';
                break;
            case 'zip':
                $contentType = 'application/zip';
                $fileExtension = '.zip';
                break;
            case 'zpl':
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
