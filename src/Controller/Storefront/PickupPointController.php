<?php declare(strict_types=1);

namespace PostNL\Shipments\Controller\Storefront;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class PickupPointController extends StorefrontController
{
    public function __construct()
    {
    }

    /**
     * @Route("/widget/checkout/postnl/pickup-point", name="frontend.checkout.postnl.pickup-point", options={"seo"=false}, methods={"POST"}, defaults={"XmlHttpRequest"=true, "csrf_protected"=true})
     * @param RequestDataBag $dataBag
     * @param SalesChannelContext $context
     * @return JsonResponse
     */
    public function setPickupPoint(RequestDataBag $data, Context $context): JsonResponse
    {
        dd($data);
        return $this->json([]);
    }
}
