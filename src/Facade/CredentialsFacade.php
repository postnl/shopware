<?php declare(strict_types=1);

namespace PostNL\Shopware6\Facade;

use Firstred\PostNL\Entity\Location;
use Firstred\PostNL\Entity\Request\GetNearestLocations;
use Firstred\PostNL\Entity\Response\GetNearestLocationsResponse;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use PostNL\Shopware6\Service\PostNL\Factory\ApiFactory;
use Psr\Log\LoggerInterface;

class CredentialsFacade
{
    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        ApiFactory      $apiFactory,
        LoggerInterface $logger
    )
    {
        $this->apiFactory = $apiFactory;
        $this->logger = $logger;
    }

    public function test(string $apiKey, bool $sandbox): bool
    {
        try {
            $this->logger->debug("Testing API key", [
                'apiKey' => $this->apiFactory->obfuscateApiKey($apiKey),
                'sandbox' => $sandbox,
            ]);

            $apiClient = $this->apiFactory->createClient($apiKey, $sandbox);

            $location = new Location();
            $location->setPostalcode('3532VA');

            $response = $apiClient->getNearestLocations(new GetNearestLocations('NL', $location));

            return $response instanceof GetNearestLocationsResponse;
        } catch (ResponseException|InvalidConfigurationException $e) {
            return false;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), [
                'exception' => (string)$e,
            ]);

            return false;
        }
    }
}
