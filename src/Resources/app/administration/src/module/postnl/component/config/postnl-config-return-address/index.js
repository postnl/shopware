import template from './postnl-config-return-address.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-config-return-address', 'memo-config', {
    template,

    mixins: [
        Mixin.getByName('memo-grid-span'),
        Mixin.getByName('postnl-config-sender-country'),
    ],

    watch: {
        senderCountry(value) {
            console.log(value);
            this.content.countrycode = value.iso;
        }
    }
})
