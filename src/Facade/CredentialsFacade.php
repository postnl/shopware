<?php declare(strict_types=1);

namespace PostNL\Shipments\Facade;

use Firstred\PostNL\Entity\Location;
use Firstred\PostNL\Entity\Request\GetNearestLocations;
use Firstred\PostNL\Entity\Response\GetNearestLocationsResponse;
use Firstred\PostNL\Exception\InvalidConfigurationException;
use Firstred\PostNL\Exception\ResponseException;
use PostNL\Shipments\Service\PostNL\Factory\ApiFactory;

class CredentialsFacade
{
    /** @var ApiFactory  */
    private $apiFactory;

    public function __construct(ApiFactory $apiFactory)
    {
        $this->apiFactory = $apiFactory;
    }

    public function test(string $apiKey, bool $sandbox): bool
    {
        try {
            $apiClient = $this->apiFactory->createClient($apiKey, $sandbox);

            $location = new Location();
            $location->setPostalcode('3532VA');

            $response = $apiClient->getNearestLocations(new GetNearestLocations('NL', $location));

            return $response instanceof GetNearestLocationsResponse;
        } catch(ResponseException | InvalidConfigurationException $e) {
            return false;
        }
    }
}
