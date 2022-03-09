import template from './postnl-order-list.html.twig'

const { Criteria } = Shopware.Data;

Shopware.Component.extend('postnl-order-list', 'sw-order-list', {
    template,

    inject: [
        'ProductSelectionService',
    ],

    data() {
        return {
            isOpenBulkShippingModal: false,
            isShippingModalId: null,

            countries: {},
            products: {}
        }
    },

    computed: {
        orderCriteria() {
            const criteria = this.$super('orderCriteria');

            criteria.addFilter(
                Criteria.equalsAny(
                    'deliveries.shippingMethod.customFields.postnl.deliveryType',
                    ['mailbox', 'shipment', 'pickup']
                )
            );
            criteria.addAssociation('deliveries.shippingOrderAddress.country');
            criteria.addAssociation('deliveries.shippingMethod');

            return criteria;
        },

        countryRepository() {
            return this.repositoryFactory.create('country');
        },

        productRepository() {
            return this.repositoryFactory.create('postnl_product');
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

                await this.loadCountries(response);
                await this.loadProducts(response);

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
                    dataIndex: 'deliveries.shippingOrderAddress.street',
                    label: 'sw-order.list.columnShippingAddress',
                    allowResize: true,
                    addAfter: 'orderCustomer.firstName'
                },
                {
                    property: 'deliveries[0].shippingMethod',
                    dataIndex: 'deliveries.shippingMethod',
                    label: 'sw-order.list.columnShippingMethod',
                    allowResize: true,
                    addAfter: 'deliveries[0].shippingOrderAddressId'
                },
                {
                    property: 'customFields.postnl.barCode',
                    dataIndex: 'customFields.postnl.barCode',
                    label: 'sw-order.list.columnBarCode',
                    allowResize: true,
                    addAfter: 'deliveries[0].shippingMethod'
                },
                {
                    property: 'customFields.postnl.status',
                    dataIndex: 'customFields.postnl.status',
                    label: 'sw-order.list.columnStatus',
                    allowResize: true,
                    addAfter: 'customFields.postnl.barCode'
                },
            ];

            const orderStatusIndex = columns.findIndex((column) => column.property === 'stateMachineState.name');
            const transactionStatusIndex = columns.findIndex((column) => column.property === 'transactions.last().stateMachineState.name');
            const deliveryStatusIndex = columns.findIndex((column) => column.property === 'deliveries[0].stateMachineState.name');

            columns[orderStatusIndex].visible = false;
            columns[transactionStatusIndex].visible = false;
            columns[deliveryStatusIndex].visible = false;

            extraColumns.forEach((extraColumn) => {
                const addAfter = extraColumn.addAfter;
                delete extraColumn.addAfter;

                const addAfterIndex = columns.findIndex((column) => column.property === addAfter);
                columns.splice(addAfterIndex + 1, 0, extraColumn);
            });

            return columns;
        },

        loadCountries(orders) {
            const countryIds = [];
            for (const order of orders) {
                countryIds.push(order.deliveries[0].shippingOrderAddress.countryId);
            }

            const criteria = new Criteria();
            criteria.setIds([...new Set(countryIds)]);

            return this.countryRepository
                .search(criteria)
                .then(result => this.countries = result)
        },

        loadProducts(orders) {
            const productIds = [];
            for (const order of orders) {
                productIds.push(order.customFields.postnl.productId);
            }

            const criteria = new Criteria();
            criteria.setIds([...new Set(productIds)]);

            return this.productRepository
                .search(criteria)
                .then(result => this.products = result)
        },
        openShippingModal(id) {
            this.isShippingModalId = id;
        },
        closeShippingModal() {
            this.isShippingModalId = null;
        },
        openBulkShippingModal() {
            this.isOpenBulkShippingModal = true;
        },
        closeBulkShippingModal() {
            this.isOpenBulkShippingModal = false;
        },

        getBarCodeLink(item) {
            const barCode = item.customFields.postnl.barCode;
            const zipcode = item.deliveries[0].shippingOrderAddress.zipcode;
            const country = Array.from(this.countries).find(country =>
                country.id === item.deliveries[0].shippingOrderAddress.countryId);

            return `http://postnl.nl/tracktrace/?B=${ barCode }&P=${ zipcode }&D=${ country.iso }&T=B`;
        },
        getProductName(item) {
            const product = Array.from(this.products).find(product =>
                product.id === item.customFields.postnl.productId);

            return product.name;
        }
    }
});
