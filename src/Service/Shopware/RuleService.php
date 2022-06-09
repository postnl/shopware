<?php

namespace PostNL\Shopware6\Service\Shopware;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Symfony\Contracts\Translation\TranslatorInterface;

class RuleService
{
    /**
     * @var EntityRepositoryInterface
     */
    private $ruleRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EntityRepositoryInterface $ruleRepository
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityRepositoryInterface $ruleRepository, LoggerInterface $logger)
    {
        $this->ruleRepository = $ruleRepository;
        $this->logger = $logger;
    }


    public function addPostNLShippingRules(Context $context)
    {
        $this->logger->info("Checking for shipping rules");
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('conditions.value.postNLZones', [
            "[\"EU\"]",
            "[\"NL\"]",
            "[\"BE\"]",
            "[\"GLOBAL\"]"
        ]));

        if ($this->ruleRepository->search($criteria, $context)->getTotal() >= 4) {
            //The rules already exist
            return;
        }

        $this->logger->info("Creating shipping rules");
        $result = $this->ruleRepository->create([
            [
                'name' => 'PostNL zone only Europe',
                'priority' => 100,
                'conditions' => [
                    [
                        'type' => 'postnlZone',
                        'value' => [
                            "operator" => "=",
                            "postNLZones" => [
                                "EU"
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'PostNL zone only Belgium',
                'priority' => 100,
                'conditions' => [
                    [
                        'type' => 'postnlZone',
                        'value' => [
                            "operator" => "=",
                            "postNLZones" => [
                                "BE"
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'PostNL zone only rest of world',
                'priority' => 100,
                'conditions' => [
                    [
                        'type' => 'postnlZone',
                        'value' => [
                            "operator" => "=",
                            "postNLZones" => [
                                "GLOBAL"
                            ]
                        ]
                    ]
                ]
            ],
            [
                'name' => 'PostNL zone only Netherlands',
                'priority' => 100,
                'conditions' => [
                    [
                        'type' => 'postnlZone',
                        'value' => [
                            "operator" => "=",
                            "postNLZones" => [
                                "NL"
                            ]
                        ]
                    ]
                ]
            ]
        ], $context);
        $this->logger->info("Shipping rule created", ['Result'=>$result]);
    }
}
