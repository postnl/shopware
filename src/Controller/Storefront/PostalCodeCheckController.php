<?php

namespace PostNL\Shopware6\Controller\Storefront;


use Exception;
use Firstred\PostNL\Exception\InvalidArgumentException;
use Firstred\PostNL\Exception\NotFoundException;
use Firstred\PostNL\Exception\PostNLException;
use PostNL\Shopware6\Facade\PostalCodeFacade;
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

    public function __construct(
        PostalCodeFacade $postalCodeFacade,
        LoggerInterface  $logger
    )
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
            if(!is_numeric($houseNumber)) {
                throw new InvalidArgumentException("Input field 'housenumber' must be a number.");
            }

            $response = $this->postalCodeFacade->checkPostalCode($context, $postalCode, $houseNumber, $houseNumberAddition);
            return $this->json($response);
        } catch (NotFoundException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            return $this->json([
                'type' => $this->postNLErrorTypeCreator($e),
                'message' => $this->trans("postnl.errors.addressNotFound"),
            ], 400);
        } catch (InvalidArgumentException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);

            return $this->json([
                'type' => $this->postNLErrorTypeCreator($e),
                'message' => $e->getMessage(),
                'field' => $this->errorFieldCreator($e->getMessage()),
            ], 400);
        } catch (PostNLException|\Throwable $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            return $this->json([
                'type' => $this->postNLErrorTypeCreator($e),
                'message' => $this->trans("postnl.errors.internalServerError"),
            ], 500);
        }
    }

    private function postNLErrorTypeCreator(\Throwable $exception): string
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
