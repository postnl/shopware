import template from './postnl-config-product-selection-base.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-config-product-selection-base', 'memo-config', {
    template,

    mixins: [
        Mixin.getByName('memo-config-access'),
        Mixin.getByName('postnl-config-sender-country'),
    ],

    data() {
        return {
            sourceZone: 'NL',
            destinationZone: 'NL',
            deliveryType: 'shipment',

            isAlt: false,
            defaultIsEnabled: !this.isAlt,
            defaultCartAmount: 0,
        }
    },

    computed: {
        showProductCode() {
            return !!this.getConfigItem('debugMode');
        }
    },

    mounted() {
        this.mountedComponent();
    },

    methods: {
        mountedComponent() {
            if(!this.content.hasOwnProperty('enabled')) {
                this.content.enabled = this.defaultIsEnabled;
                this.updateValue(this.content);
            }
            if(!this.content.hasOwnProperty('cartAmount')) {
                this.content.cartAmount = this.defaultCartAmount;
                this.updateValue(this.content);
            }
        },
    },

})
