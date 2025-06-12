import template from './postnl-config-return-address.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-config-return-address', 'postnl-memo-config', {
    template,

    mixins: [
        Mixin.getByName('postnl-memo-grid-span'),
        Mixin.getByName('postnl-config-sender-country'),
    ],

    watch: {
        senderCountry(value) {
            this.content.countrycode = value.iso;
        }
    }
})
