import template from './sw-order-user-card.html.twig';

const { Component } = Shopware;

Component.override('sw-order-user-card', {
    template,

    inject: [
        'ProductSelectionService'
    ],

    data() {
        return {
            postnl: {
                product: null,
            }
        }
    },

    computed: {
        isPostNLOrder() {
            return 'postnl' in this.currentOrder.customFields;
        },
    },

    methods: {
        reload() {
            this.$super('reload');
            this.getPostNLProduct();
        },

        getPostNLProduct() {
            const id = this.currentOrder.customFields.postnl.productId;

            return this.ProductSelectionService
                .getProduct(id)
                .then(result => this.postnl.product = result.product);
        }
    }
});
