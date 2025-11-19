import nlNL from './snippet/nl-NL.json';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

export default {
    type: 'core',
    name: 'PostNL',
    title: 'postnl.general.mainMenuItemGeneral',
    description: 'postnl.general.descriptionTextModule',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#ed7000',

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
        detail: {
            component: 'postnl-order-detail',
            path: 'detail/:id',
            redirect: {
                name: 'postnl.order.detail.general',
            },
            meta: {
                privilege: 'order.viewer',
                appSystem: {
                    view: 'detail',
                },
            },
            children: orderDetailChildren(),
            props: {
                default: ($route) => {
                    return { orderId: $route.params.id };
                },
            },
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
}

function orderDetailChildren() {
    return {
        general: {
            component: 'sw-order-detail-general',
            path: 'general',
            meta: {
                parentPath: 'postnl.order.index',
                privilege: 'order.viewer',
            },
        },
        details: {
            component: 'sw-order-detail-details',
            path: 'details',
            meta: {
                parentPath: 'postnl.order.index',
                privilege: 'order.viewer',
            },
        },
        documents: {
            component: 'sw-order-detail-documents',
            path: 'documents',
            meta: {
                parentPath: 'postnl.order.index',
                privilege: 'order.viewer',
            },
        },
    };

}

