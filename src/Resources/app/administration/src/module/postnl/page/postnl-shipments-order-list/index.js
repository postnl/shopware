
const {Criteria} = Shopware.Data;

Shopware.Component.extend('postnl-shipments-order-list', 'sw-order-list', {
    computed: {
        orderCriteria() {
            const criteria = this.$super('orderCriteria');

            criteria.addFilter(Criteria.equalsAny('deliveries.shippingMethod.id', ['4fde4d23cafb443bb91b34057aaa0af7']))

            return criteria;
        },

        listFilters() {
            const filters = this.$super('listFilters');
            return filters.filter(filter => filter.name !== 'shipping-method-filter');
        },
    },

    methods: {
        async getList() {
            await this.$super('getList');

            let criteria = await Shopware.Service('filterService')
                .mergeWithStoredFilters(this.storeKey, this.orderCriteria);

            this.activeFilterNumber = criteria.filters.length - 1;
        },
    }
});
