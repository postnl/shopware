<?php declare(strict_types=1);

namespace PostNL\Shopware6\Controller\Storefront;

use PostNL\Shopware6\Service\Shopware\CartService;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class PickupPointController extends StorefrontController
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route(path: '/widget/checkout/postnl/pickup-point', name: 'frontend.checkout.postnl.pickup-point', options: ['seo' => false], defaults: ['XmlHttpRequest' => true], methods: ['POST'])]
    public function setPickupPoint(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $pickupPointLocationCode = $data->get('pickupPointLocationCode');

        $this->cartService->addData([
            'pickupPointLocationCode' => (int)$pickupPointLocationCode
        ], $context);

        return $this->json(null, 204);
    }
}
