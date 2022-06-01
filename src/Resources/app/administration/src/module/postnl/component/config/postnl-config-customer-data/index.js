import template from './postnl-config-customer-data.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-config-customer-data', 'memo-config', {
    template,

    mixins: [
        Mixin.getByName('memo-grid-span')
    ]
})
