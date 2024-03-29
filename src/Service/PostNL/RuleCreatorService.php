<?php

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Service\Shopware\RuleService;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RuleCreatorService
{
    public function create(ActivateContext $activateContext, ContainerInterface $container):array
    {
        $ruleService = new RuleService(
            $container->get('rule.repository'),
            $container->get('postnl.logger')
        );
        return $ruleService->addPostNLShippingRules($activateContext->getContext());
    }
}
