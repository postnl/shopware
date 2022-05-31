import template from './postnl-config-customer-data.html.twig';
import '../postnl-config.scss';

const { Component, Mixin } = Shopware;

Component.extend('postnl-config-customer-data', 'memo-config', {
    template,

    mixins: [
        Mixin.getByName('memo-grid-span')
    ]
})
