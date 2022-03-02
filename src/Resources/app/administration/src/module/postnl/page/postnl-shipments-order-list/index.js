import template from './postnl-shipments-order-list.html.twig'

const {Criteria} = Shopware.Data;

Shopware.Component.extend('postnl-shipments-order-list', 'sw-order-list', {
    template,

    inject: [
        'ProductSelectionService'
    ],

    data() {
        return {
            isOpenBulkShippingModal: false,
            isShippingModalId: null,
        }
    },

    computed: {
        orderCriteria() {
            const criteria = this.$super('orderCriteria');

            criteria.addFilter(
                Criteria.equalsAny(
                    'deliveries.shippingMethod.customFields.postnl_shipments.deliveryType',
                    ['mailbox', 'shipment', 'pickup']
                )
            );
            criteria.addAssociation('deliveries.shippingOrderAddress.country');
            criteria.addAssociation('deliveries.shippingMethod');

            return criteria;
        },

        listFilters() {
            const filters = this.$super('listFilters');
            return filters.filter(filter => filter.name !== 'shipping-method-filter');
        },
    },

    methods: {
        async getList() {
            this.isLoading = true;

            let criteria = await Shopware.Service('filterService')
                .mergeWithStoredFilters(this.storeKey, this.orderCriteria);

            criteria = await this.addQueryScores(this.term, criteria);

            this.activeFilterNumber = criteria?.filters?.length - 1 ?? 0;

            try {
                const response = await this.orderRepository.search(criteria);

                this.total = response.total;
                this.orders = response;
                this.isLoading = false;
            } catch {
                this.isLoading = false;
            }
        },

        getOrderColumns() {
            let columns = this.$super('getOrderColumns');

            columns = columns.filter((column) => {
                return !['salesChannel.name', 'billingAddressId', 'amountTotal', 'affiliateCode', 'campaignCode'].includes(column.property);
            });

            const extraColumns = [
                {
                    property: 'deliveries[0].shippingOrderAddressId',
                    dataIndex: 'deliveries[0].shippingOrderAddress.street',
                    label: 'sw-order.list.columnShippingAddress',
                    allowResize: true,
                    addAfter: 'orderCustomer.firstName'
                },
                {
                    property: 'deliveries[0].shippingMethod',
                    dataIndex: 'deliveries[0].shippingMethod',
                    label: 'sw-order.list.columnShippingMethod',
                    allowResize: true,
                    addAfter: 'deliveries[0].shippingOrderAddressId'
                },
            ];


            extraColumns.forEach((extraColumn) => {
                const addAfter = extraColumn.addAfter;
                delete extraColumn.addAfter;

                const addAfterIndex = columns.findIndex((column) => column.property === addAfter);
                columns.splice(addAfterIndex + 1, 0, extraColumn);
            });

            return columns;
        },

        openShippingModal(id) {
            this.isShippingModalId = id;
        },
        closeShippingModal() {
            this.isShippingModalId = null;
        },
        openBulkShippingModal() {
            this.isOpenBulkShippingModal = true;
        } ,
        closeBulkShippingModal() {
            this.isOpenBulkShippingModal = false;
        },
    }
});
