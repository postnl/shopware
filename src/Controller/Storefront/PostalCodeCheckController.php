<?php

namespace PostNL\Shopware6\Controller\Storefront;


use Exception;
use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shopware6\Facade\PostalCodeFacade;
use PostNL\Shopware6\Service\PostNL\Api\Exception\AddressNotFoundException;
use PostNL\Shopware6\Service\PostNL\Api\Exception\InvalidAddressException;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class PostalCodeCheckController extends StorefrontController
{
    private PostalCodeFacade $postalCodeFacade;
    private LoggerInterface $logger;

    public function __construct(PostalCodeFacade $postalCodeFacade,
                                LoggerInterface  $logger)
    {
        $this->postalCodeFacade = $postalCodeFacade;
        $this->logger = $logger;

    }

    #[Route(path: '/widget/address/postnl/postalcode-check', name: 'frontend.address.postnl.postal-code-check', options: ['seo' => false], defaults: ['XmlHttpRequest' => true], methods: ['POST'])]
    public function getPostcodeCheck(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $postalCode = $data->get('postalCode');
        $houseNumber = $data->get('houseNumber');
        $houseNumberAddition = $data->get('houseNumberAddition');

        try {
            $response = $this->postalCodeFacade->checkPostalCode($context, $postalCode, $houseNumber, $houseNumberAddition);
            return $this->json($response);

        } catch (InvalidAddressException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            if ($e->getMessage()==""){
                $translatedMessage = $this->trans("postnl.errors.addressNotFound");
            }else{
                $translatedMessage = $this->postNLErrorMessageCreator($e->getMessage());
            }

            return $this->json([
                'errorType' => $this->postNLErrorTypeCreator($e),
                'errorMessage' => $translatedMessage,
                'errorField' => $this->errorFieldCreator($e->getMessage())]);

        } catch (AddressNotFoundException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            return $this->json([
                'errorType' => $this->postNLErrorTypeCreator($e),
                'errorMessage' => $this->trans("postnl.errors.addressNotFound")
                ]);
        } catch (PostNLException|Exception $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return $this->json($this->trans("postnl.errors.internalServerError"), 501);
        }
    }

    private function postNLErrorTypeCreator(Exception $exception): string
    {
        $explode = explode('\\', get_class($exception));
        return end($explode);
    }

    private function postNLErrorMessageCreator(string $message): string
    {
        $translation = $this->trans("postnl.errors.api." . $message);
        if (str_starts_with($translation, "postnl.errors.api.")) {
            return $message;
        } else {
            return $translation;
        }
    }

    private function errorFieldCreator(string $error): string
    {
        $errorFields = ['housenumber', 'postalcode'];
        foreach ($errorFields as $errorField) {
            if (str_contains($error, $errorField)) {
                return $errorField;
            }
        }
        return "";
    }
}
