Shopware.Component.extend('postnl-zone', 'sw-condition-base', () => import('../core/component/postnl-zone'));

Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('postnlZone', {
        component: 'postnl-zone',
        label: 'postnl.rules.shipping.name',
        scopes: ['checkout'],
        group: 'order',
    });

    return ruleConditionService;
});
