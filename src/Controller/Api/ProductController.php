<?php

namespace PostNL\Shopware6\Controller\Api;

use PostNL\Shopware6\Facade\ProductFacade;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Validation\DataBag\QueryDataBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class ProductController extends AbstractController
{
    protected ProductFacade $productFacade;

    protected LoggerInterface $logger;

    public function __construct(
        ProductFacade   $productFacade,
        LoggerInterface $logger
    )
    {
        $this->productFacade = $productFacade;
        $this->logger = $logger;
    }

    #[Route(path: '/api/_action/postnl/product/source-zone', name: 'api.action.postnl.product.source', defaults: ['auth_enabled' => true], methods: ['GET'])]
    public function sourceZoneHasProducts(QueryDataBag $query, Context $context)
    {
        $sourceZone = $query->get('sourceZone');

        //TODO check if vars are empty
        //TODO catch errors

        return $this->json([
            'hasProducts' => $this->productFacade->sourceZoneHasProducts($sourceZone, $context),
        ]);
    }

    #[Route(path: '/api/_action/postnl/product/delivery-types', name: 'api.action.postnl.product.delivery-types', defaults: ['auth_enabled' => true], methods: ['GET'])]
    public function getDeliveryTypes(QueryDataBag $query, Context $context)
    {
        $sourceZone = $query->get('sourceZone');
        $destinationZone = $query->get('destinationZone');

        return $this->json($this->productFacade->getDeliveryTypes($sourceZone, $destinationZone, $context));
    }

    #[Route(path: '/api/_action/postnl/product/flags', name: 'api.action.postnl.product.flags', defaults: ['auth_enabled' => true], methods: ['GET'])]
    public function flags(QueryDataBag $query, Context $context): JsonResponse
    {
        $productId = $query->get('productId');

        $flags = $this->productFacade->getFlagsForProduct($productId, $context);

        return $this->json([
            'flags' => $flags,
        ]);
    }

    #[Route(path: '/api/_action/postnl/product/flags/available', name: 'api.action.postnl.product.flags.available', defaults: ['auth_enabled' => true], methods: ['GET'])]
    public function availableFlags(QueryDataBag $query, Context $context): JsonResponse
    {
        $sourceZone = $query->get('sourceZone');
        $destinationZone = $query->get('destinationZone');
        $deliveryType = $query->get('deliveryType');

        $flags = $this->productFacade->getFlags($sourceZone, $destinationZone, $deliveryType, $context);

        return $this->json([
            'flags' => $flags,
        ]);
    }

    #[Route(path: '/api/_action/postnl/product', name: 'api.action.postnl.product', defaults: ['auth_enabled' => true], methods: ['GET'])]
    public function product(QueryDataBag $query, Context $context): JsonResponse
    {
        $productId = $query->get('productId');

        $product = $this->productFacade->getProduct($productId, $context);

        return $this->json([
            'product' => $product,
        ]);
    }

    #[Route(path: '/api/_action/postnl/product/default', name: 'api.action.postnl.product.default', defaults: ['auth_enabled' => true], methods: ['GET'])]
    public function defaultProduct(QueryDataBag $query, Context $context)
    {
        $sourceZone = $query->get('sourceZone');
        $destinationZone = $query->get('destinationZone');
        $deliveryType = $query->get('deliveryType');

        $product = $this->productFacade->getDefaultProduct($sourceZone, $destinationZone, $deliveryType, $context);

        return $this->json([
            'product' => $product,
        ]);
    }

    #[Route(path: '/api/_action/postnl/product/select', name: 'api.action.postnl.product.select', defaults: ['auth_enabled' => true], methods: ['GET'])]
    public function selectProduct(QueryDataBag $query, Context $context)
    {
        $sourceZone = $query->get('sourceZone');
        $destinationZone = $query->get('destinationZone');
        $deliveryType = $query->get('deliveryType');
        $flags = $query->get('flags')->all();
        $changedFlags = $query->get('changedFlags')->all();

        $product = $this->productFacade->selectProduct(
            $sourceZone,
            $destinationZone,
            $deliveryType,
            $flags,
            $changedFlags,
            $context
        );

        return $this->json([
            'product' => $product,
        ]);
    }
}
