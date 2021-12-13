<?php

namespace PostNl\Shipments\Controller\Api;

use PostNl\Shipments\Facade\CredentialsFacade;
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
    private $credentialsFacade;

    public function __construct(CredentialsFacade $credentialsFacade)
    {
        $this->credentialsFacade = $credentialsFacade;
    }

    /**
     * @Route("/api/_action/postnl/credentials/test",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.credentials.test", methods={"POST"})
     *
     * @param Request $request
     * @param Context $context
     * @return JsonResponse
     */
    public function test(Request $request): JsonResponse
    {
        // Get the live API key
        $apiKey = $request->get('apiKey');

        return $this->getTestResponse($apiKey);
    }

    /**
     * @Route("/api/v{version}/_action/postnl/credentials/test",
     *         defaults={"auth_enabled"=true}, name="api.action.postnl.credentials.test.legacy", methods={"POST"})
     *
     * @param Request $request
     * @param Context $context
     * @return JsonResponse
     */
    public function testLegacy(Request $request): JsonResponse
    {
        // Get the live API key
        $apiKey = $request->get('apiKey');

        return $this->getTestResponse($apiKey);
    }

    /**
     * @param $apiKey
     * @return JsonResponse
     */
    private function getTestResponse($apiKey): JsonResponse
    {
        $this->credentialsFacade->test($apiKey);

        return $this->json([]);
    }
}
