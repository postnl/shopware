import template from './postnl-config-product-selection-base.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-config-product-selection-base', 'memo-config', {
    template,

    mixins: [
        Mixin.getByName('memo-config-access'),
    ],

    data() {
        return {
            sourceZone: 'NL',
            destinationZone: 'NL',
            deliveryType: 'shipment',

            isAlt: false,
            defaultIsEnabled: true,
            defaultCartAmount: 0,
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
