<?php

namespace PostNL\Shipments\Controller\Api;

use PostNL\Shipments\Facade\ProductFacade;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\QueryDataBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class ProductController extends AbstractController
{
    /**
     * @var ProductFacade
     */
    protected $productFacade;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ProductFacade   $productFacade,
        LoggerInterface $logger
    )
    {
        $this->productFacade = $productFacade;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/_action/postnl/product/source",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.product.source", methods={"GET"})
     *
     * @param QueryDataBag $query
     * @param Context $context
     * @return JsonResponse
     */
    public function source(QueryDataBag $query, Context $context)
    {
        $sourceZone = $query->get('sourceZone');

        //TODO check if vars are empty
        //TODO catch errors

        return $this->json([
            'hasProducts' => $this->productFacade->sourceZoneHasProducts($sourceZone, $context)
        ]);
    }

    /**
     * @Route("/api/_action/postnl/product/delivery-types",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.product.delivery-types", methods={"GET"})
     *
     * @param QueryDataBag $query
     * @param Context $context
     * @return JsonResponse
     */
    public function deliveryTypes(QueryDataBag $query, Context $context)
    {
        $sourceZone = $query->get('sourceZone');
        $destinationZone = $query->get('destinationZone');

        return $this->json($this->productFacade->getAvailableDeliveryTypes($sourceZone, $destinationZone, $context));
    }




    /**
     * @Route("/api/_action/postnl/product/select",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.product.select", methods={"GET"})
     *
     * @param QueryDataBag $query
     * @param Context $context
     * @return JsonResponse
     */
    public function select(QueryDataBag $query, Context $context)
    {
        $sourceZone = $query->get('sourceZone');
        $destinationZone = $query->get('destinationZone');
        $deliveryType = $query->get('deliveryType');
        $options = json_decode($query->get('options', '{}'), true);

        //TODO check if vars are empty
        //TODO catch errors

        $product = $this->productFacade->select($sourceZone, $destinationZone, $deliveryType, $options, $context);
        $options = $this->productFacade->options($sourceZone, $destinationZone, $deliveryType, $options, $context);

        return $this->json([
            'product' => $product,
            'options' => $options,
        ]);
    }




    /**
     * @Route("/api/_action/postnl/product/options",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.product.options", methods={"GET"})
     *
     * @param QueryDataBag $query
     * @param Context $context
     * @return JsonResponse
     */
    public function options(QueryDataBag $query, Context $context)
    {
        $sourceZone = $query->get('sourceZone');
        $destinationZone = $query->get('destinationZone');
        $deliveryType = $query->get('deliveryType');

        $options = $this->productFacade->getAvailableOptions($sourceZone, $destinationZone, $deliveryType, $context);

        return $this->json([
            'options' => $options,
        ]);
    }

}
