<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Controller\Storefront;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Shopware\CartService;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class ShippingController extends StorefrontController
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
     * @throws \Exception
     */
    public function setPickupPoint(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {

        $deliveryDate = $data->get('deliveryDate');
        $dateDateTime = new \DateTime($deliveryDate,new \DateTimeZone("UTC"));//Even though its CET

        $this->cartService->addData([
            Defaults::CUSTOM_FIELDS_DELIVERY_DATE_KEY => date_format($dateDateTime,DATE_ATOM)
        ], $context);

        return $this->json(null, 204);
    }
}
