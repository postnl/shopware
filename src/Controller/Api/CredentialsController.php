<?php declare(strict_types=1);

namespace PostNL\Shopware6\Controller\Api;

use PostNL\Shopware6\Facade\CredentialsFacade;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['api']])]
class CredentialsController extends AbstractController
{
    protected CredentialsFacade $credentialsFacade;
    protected LoggerInterface $logger;

    public function __construct(
        CredentialsFacade $credentialsFacade,
        LoggerInterface   $logger
    )
    {
        $this->credentialsFacade = $credentialsFacade;
        $this->logger = $logger;
    }

    #[Route(path: '/api/_action/postnl/credentials/test', name: 'api.action.postnl.credentials.test', defaults: ['auth_enabled' => true], methods: ['POST'])]
    public function test(Request $request): JsonResponse
    {
        $apiKey = $request->get('apiKey');
        $sandbox = $request->get('sandbox');

        $valid = $this->credentialsFacade->test($apiKey, $sandbox);

        $this->logger->info("API key validated", [
            'sandbox' => $sandbox,
            'valid' => $valid,
        ]);

        return $this->json(['valid' => $valid]);
    }
}
