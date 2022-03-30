<?php

namespace PostNL\Shopware6\Controller\Storefront;


use Exception;
use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shopware6\Facade\PostalCodeFacade;
use PostNL\Shopware6\Service\PostNL\ApiExtension\Exception\InvalidAddressException;
use Psr\Log\LoggerInterface;
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
    private LoggerInterface $logger;
    public function __construct(PostalCodeFacade $postalCodeFacade,
                                LoggerInterface $logger)
    {
        $this->postalCodeFacade = $postalCodeFacade;
        $this->logger = $logger;

    }

    /**
     * @Route("/widget/address/postnl/postalcode-check", name="frontend.address.postnl.postal-code-check", options={"seo"=false}, methods={"POST"}, defaults={"XmlHttpRequest"=true, "csrf_protected"=true})
     * @param RequestDataBag $data
     * @param SalesChannelContext $context
     * @return JsonResponse
     */
    public function getPostcodeCheck(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $postalCode = $data->get('postalCode');
        $houseNumber = $data->get('houseNumber');
        $houseNumberAddition = $data->get('houseNumberAddition');

        try {
            $response = $this->postalCodeFacade->checkPostalCode($context,$postalCode,$houseNumber,$houseNumberAddition);

            return $this->json($response);

        }catch (InvalidAddressException $e){
            $this->logger->error($e->getMessage(),['exception'=>$e]);
            $translatedMessage = $this->postNLErrorCreator($e->getMessage());

            return $this->json(['error'=>$translatedMessage]);

        }catch (PostNLException|Exception $e){
            $this->logger->error($e->getMessage(),['exception'=>$e]);

            return $this->json($this->trans("postnl.errors.internalServerError"),501);
        }
    }

    private function postNLErrorCreator(string $message): string
    {
        $translation = $this->trans("postnl.errors.api." . $message);
        if (str_starts_with($translation, "postnl.errors.api.")){
            return $message;
        }else{
            return $translation;
        }
    }
}
