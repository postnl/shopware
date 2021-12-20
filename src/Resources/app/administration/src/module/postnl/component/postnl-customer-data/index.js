import template from './postnl-customer-data.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-customer-data', 'memo-config', {
    template,

    mixins: [
        Mixin.getByName('memo-grid-span')
    ]
})
