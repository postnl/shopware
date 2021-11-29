import './component';
import './page';
import './view';

// import nlNL from './snippet/nl-NL.json';
// import deDE from './snippet/de-DE.json';
// import enGB from './snippet/en-GB.json';

Shopware.Module.register('postnl-shipments', {
    type: 'core',
    name: 'PostNL',
    title: 'postnl-shipments.general.mainMenuItemGeneral',
    description: 'postnl-shipments.general.descriptionTextModule',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#ed7000',
    icon: 'default-package-closed',

    snippets: {
        // 'nl-NL': nlNL,
        // 'de-DE': deDE,
        // 'en-GB': enGB
    },

    routes: {
        orders: {
            component: 'postnl-shipments-order-list',
            path: 'orders'
        },
    },

    navigation: [
        {
            id: 'postnl-shipments-orders',
            label: 'postnl-shipments.general.ordersMenuItemGeneral',
            icon: 'default-package-closed',
            color: '#ed7000',
            path: 'postnl.shipments.orders',
            parent: 'sw-order'
        }
    ]
})
