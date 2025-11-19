import template from './sw-order-user-card.html.twig';

export default {
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
            return this.currentOrder.customFields !== null && 'postnl' in this.currentOrder.customFields;
        },
    },

    methods: {
        reload() {
            this.$super('reload');

            if(this.isPostNLOrder) {
                this.getPostNLProduct();
            }
        },

        getPostNLProduct() {
            if(!this.isPostNLOrder) {
                return null;
            }

            const id = this.currentOrder.customFields.postnl.productId;

            return this.ProductSelectionService
                .getProduct(id)
                .then(result => this.postnl.product = result.product);
        }
    }
}
