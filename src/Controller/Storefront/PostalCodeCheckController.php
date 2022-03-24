<?php

namespace PostNL\Shopware6\Controller\Storefront;


use PostNL\Shopware6\Facade\PostalCodeFacade;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class PostalCodeCheckController extends StorefrontController
{
    private PostalCodeFacade $postalCodeFacade;
    public function __construct(PostalCodeFacade $postalCodeFacade)
    {
        $this->postalCodeFacade = $postalCodeFacade;
    }

    /**
     * @Route("/widget/address/postnl/postalcode-check", name="frontend.address.postnl.postal-code-check", options={"seo"=false}, methods={"POST"}, defaults={"XmlHttpRequest"=true, "csrf_protected"=true})
     * @param RequestDataBag $data
     * @param SalesChannelContext $context
     * @return JsonResponse
     */
    public function setPickupPoint(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $postalCode = $data->get('postalCode');
        $houseNumber = $data->get('houseNumber');
        $houseNumberAddition = $data->get('houseNumberAddition');

        $response = $this->postalCodeFacade->checkPostalCode($context,$postalCode,$houseNumber,$houseNumberAddition);
dd($response);
        return $this->json($response);
    }
}
