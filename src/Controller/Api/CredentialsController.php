<?php declare(strict_types=1);

namespace PostNL\Shipments\Controller\Api;

use PostNL\Shipments\Facade\CredentialsFacade;
use PostNL\Shipments\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shipments\Service\PostNL\ProductCode\ProductService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
class CredentialsController extends AbstractController
{
    /**
     * @var CredentialsFacade
     */
    protected $credentialsFacade;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        CredentialsFacade $credentialsFacade,
        LoggerInterface   $logger
    )
    {
        $this->credentialsFacade = $credentialsFacade;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/_action/postnl/credentials/test",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.credentials.test", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function test(Request $request): JsonResponse
    {
        $apiKey = $request->get('apiKey');
        $sandbox = $request->get('sandbox');

        return $this->getTestResponse($apiKey, $sandbox);
    }

    /**
     * @Route("/api/v{version}/_action/postnl/credentials/test",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.credentials.test.legacy", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function testLegacy(Request $request): JsonResponse
    {
        $apiKey = $request->get('apiKey');
        $sandbox = $request->get('sandbox');

        return $this->getTestResponse($apiKey, $sandbox);
    }

    /**
     * @param string $apiKey
     * @param bool $sandbox
     * @return JsonResponse
     */
    private function getTestResponse(string $apiKey, bool $sandbox): JsonResponse
    {
        $context = Context::createDefaultContext();
        $service = $this->container->get(ProductService::class);


        $hasProducts = $service->sourceZoneHasProducts('NL', $context);

        $deliveryTypes = $service->getAvailableDeliveryTypes('NL', 'NL', $context);

        $defaultProduct = $service->getDefaultProduct('NL', 'NL', DeliveryType::SHIPMENT, $context);


        dd($hasProducts, $deliveryTypes, $defaultProduct);

        $valid = $this->credentialsFacade->test($apiKey, $sandbox);

        $this->logger->info("API key validated", [
            'sandbox' => $sandbox,
            'valid' => $valid,
        ]);

        return $this->json(['valid' => $valid]);
    }
}
