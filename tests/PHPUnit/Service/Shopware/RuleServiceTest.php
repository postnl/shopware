<?php
declare(strict_types=1);

namespace PostNL\tests\Service\Shopware;

use PostNL\Shopware6\Service\Shopware\RuleService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

/**
 * @coversDefaultClass \PostNL\Shopware6\Service\Shopware\RuleService
 */
class RuleServiceTest extends TestCase
{

    private function createRuleService(
        EntityRepositoryInterface $ruleRepository = null,
        LoggerInterface           $logger = null
    )
    {
        if (!$ruleRepository) {
            $ruleRepository = $this->createMock(EntityRepositoryInterface::class);
        }

        if (!$logger) {
            $logger = $this->createMock(LoggerInterface::class);
        }

        return new RuleService($ruleRepository, $logger);
    }

    /**
     * @covers ::addPostNLShippingRules()
     * @covers ::addPostNLShippingRule()
     * @return void
     */
    public function testAddPostNLShippingRules()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $ruleRepository = $this->createMock(EntityRepositoryInterface::class);
        $ruleService = $this->createRuleService($ruleRepository, $logger);
        $context = $this->createMock(Context::class);
        $result = $ruleService->addPostNLShippingRules($context);
        $keys = [
            "PostNL zone only Europe",
            "PostNL zone only Belgium",
            "PostNL zone only rest of world",
            "PostNL zone only Netherlands",
        ];
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key,$result);
        }

    }

    /**
     * @covers ::__construct()
     * @return void
     */
    public function test__construct()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $ruleRepository = $this->createMock(EntityRepositoryInterface::class);
        $ruleService = $this->createRuleService($ruleRepository, $logger);
        $this->assertInstanceOf(RuleService::class, $ruleService);
    }
}
