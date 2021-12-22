<?php

namespace PostNl\Shipments\Controller\Api;

use PostNl\Shipments\Facade\CredentialsFacade;
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
     * @param $apiKey
     * @param $sandbox
     * @return JsonResponse
     */
    private function getTestResponse($apiKey, $sandbox): JsonResponse
    {
        $valid = $this->credentialsFacade->test($apiKey, $sandbox);

        return $this->json(['valid' => $valid]);
    }
}
