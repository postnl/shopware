import template from './postnl-config-product-selection-base.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-config-product-selection-base', 'memo-config', {
    template,

    inject: [
        'ProductSelectionService',
    ],

    mixins: [
        Mixin.getByName('postnl-memo-config-access'),
        Mixin.getByName('postnl-config-sender-country'),
    ],

    data() {
        return {
            product: null,
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
        },
    },

    watch: {
        content: {
            handler(value) {
                this.updateValue(value);
                this.updateHeader();
            },
            deep: true,
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
            this.updateHeader();
        },

        updateHeader() {
            return Promise.resolve()
                .then(() => {
                    if(this.content?.id) {
                        return this.ProductSelectionService.getProduct(this.content.id);
                    } else {
                        return this.ProductSelectionService.getDefaultProduct(
                            this.sourceZone,
                            this.destinationZone,
                            this.deliveryType
                        );
                    }
                })
                .then(result => result.product)
                .then(product => this.product = product);
        }
    },

})
