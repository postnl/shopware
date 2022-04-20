import './component';
import './page';

import nlNL from './snippet/nl-NL.json';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

Shopware.Module.register('postnl-order', {
    type: 'core',
    name: 'PostNL',
    title: 'postnl.general.mainMenuItemGeneral',
    description: 'postnl.general.descriptionTextModule',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#ed7000',
    icon: 'postnl-ghost',

    snippets: {
        'nl-NL': nlNL,
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: 'postnl-order-list',
            path: 'index'
        },
    },

    navigation: [
        {
            id: 'postnl-order-index',
            label: 'postnl.general.ordersMenuItemGeneral',
            icon: 'default-package-closed',
            color: '#ed7000',
            path: 'postnl.order.index',
            parent: 'sw-order'
        }
    ]
})
