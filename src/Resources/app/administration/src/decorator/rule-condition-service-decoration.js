import '../core/component/postnl-zone';

import deDE from '../module/postnl/snippet/de-DE.json';
import enGB from '../module/postnl/snippet/en-GB.json';
import nlNL from '../module/postnl/snippet/nl-NL.json';

Shopware.Locale.extend('de-DE', deDE);
Shopware.Locale.extend('en-GB', enGB);
Shopware.Locale.extend('nl-NL', nlNL);

Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('postnlZone', {
        component: 'postnl-zone',
        label: 'postnl.rules.shipping.name',
        scopes: ['checkout']
    });

    return ruleConditionService;
});
