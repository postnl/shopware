import template from './sw-order-user-card.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.override('sw-order-user-card', {
    template,

    inject:[
        'repositoryFactory'
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

        productRepository() {
            return this.repositoryFactory.create('postnl_product');
        },
    },

    methods: {
        reload() {
            this.$super('reload');
            this.getPostNLProduct();
        },

        getPostNLProduct() {
            const id = this.currentOrder.customFields.postnl.productId;

            const criteria = new Criteria();
            criteria.setIds([id]);

            return this.productRepository.search(criteria, Shopware.Context.api)
                .then(result => {
                    this.postnl.product = result.first();
                });
        }
    }
});
