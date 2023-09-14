<?php

namespace PostNL\Shopware6\Service\Shopware;

use PostNL\Shopware6\Defaults;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;

class RuleService
{
    /**
     * @var EntityRepository
     */
    private $ruleRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityRepository    $ruleRepository
     * @param LoggerInterface     $logger
     * @param TranslatorInterface $translator
     */
    public function __construct($ruleRepository, LoggerInterface $logger)
    {
        $this->ruleRepository = $ruleRepository;
        $this->logger = $logger;
    }


    public function addPostNLShippingRules(Context $context): array
    {

        $zoneArray = [
            Defaults::ZONE_ONLY_EUROPE => "EU",
            Defaults::ZONE_ONLY_BELGIUM => "BE",
            Defaults::ZONE_ONLY_REST_OF_WORLD => "GLOBAL",
            Defaults::ZONE_ONLY_NETHERLANDS => "NL",
        ];

        $resultArray = [];

        foreach ($zoneArray as $zoneName => $zone) {
            $resultArray[$zoneName] = $this->addPostNLShippingRule($zoneName, $zone, $context);
        }

        return $resultArray;
    }

    /**
     * @param string  $name
     * @param string  $zone
     * @param Context $context
     * @return string|null id
     */
    private function addPostNLShippingRule(string $name, string $zone, Context $context): ?string
    {
        $this->logger->info("Checking for shipping rule");
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('conditions.value.postNLZones', "[\"" . $zone . "\"]")
        );

        $result = $this->ruleRepository->search($criteria, $context);

        if ($result->getTotal() != 0) {
            //The rules already exist
            return $result->first()->getId();
        }
        $this->logger->info("Creating shipping rules");

        $id = Uuid::randomHex();

        $result = $this->ruleRepository->create([
            [
                'id' => $id,
                'name' => $name,
                'priority' => 100,
                'conditions' => [
                    [
                        'type' => 'postnlZone',
                        'value' => [
                            "operator" => "=",
                            "postNLZones" => [
                                $zone,
                            ],
                        ],
                    ],
                ],
            ],
        ], $context);
        $this->logger->info("Shipping rule created", ['Result' => $result]);

        return $id;
    }
}
