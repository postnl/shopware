Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('postnlZone', {
        component: 'postnl-zone',
        label: 'postnl.rules.shipping.name',
        scopes: ['checkout'],
        group: 'order',
    });

    return ruleConditionService;
});
