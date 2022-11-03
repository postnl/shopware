<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Controller\Storefront;

use PostNL\Shopware6\Service\Shopware\CartService;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ShippingController
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/widget/checkout/postnl/shipping-date", name="frontend.checkout.postnl.shipping-date", options={"seo"=false}, methods={"POST"}, defaults={"XmlHttpRequest"=true, "csrf_protected"=true})
     * @param RequestDataBag $data
     * @param SalesChannelContext $context
     * @return JsonResponse
     */
    public function setPickupPoint(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
//        $pickupPointLocationCode = $data->get('pickupPointLocationCode');
//
//        $this->cartService->addData([
//            'pickupPointLocationCode' => (int)$pickupPointLocationCode
//        ], $context);
//
//        return $this->json(null, 204);
    }
}
