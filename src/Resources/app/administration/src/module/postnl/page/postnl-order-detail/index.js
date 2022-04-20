import template from './postnl-order-detail.html.twig';

const { Component } = Shopware;

Component.extend('postnl-order-detail', 'sw-order-detail', {
    template,

    created() {
        this.reloadEntityData();
    }
})
