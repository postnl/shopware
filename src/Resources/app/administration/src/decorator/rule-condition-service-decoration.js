import '../core/component/postnl-zone';

Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('postnlZone', {
        component: 'postnl-zone',
        label: 'PostNL Zone Rule',
        scopes: ['checkout']
    });

    return ruleConditionService;
});
