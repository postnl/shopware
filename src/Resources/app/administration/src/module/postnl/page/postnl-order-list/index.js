import template from './postnl-order-list.html.twig'

const { Criteria } = Shopware.Data;

Shopware.Component.extend('postnl-order-list', 'sw-order-list', {
    template,

    inject: [
        'ProductSelectionService',
    ],

    data() {
        return {
            isBulkChangeShippingModalOpen: false,
            isBulkCreateShipmentModalOpen: false,
            isChangeShippingModalId: null,
            isCreateShipmentModalId: null,

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

            const columnMap = {};
            columns.forEach(column => {
                const key = (column.dataIndex ?? column.property).replace(/\.|,/g, '-');
                columnMap[key] = column;
            });

            const extraColumns = [
                {
                    property: 'deliveries[0].shippingOrderAddressId',
                    dataIndex: 'deliveries.shippingOrderAddress.street',
                    label: 'postnl.order.list.columnShippingAddress',
                    allowResize: true,
                },
                {
                    property: 'customFields.postnl.productId',
                    dataIndex: 'customFields.postnl.productId',
                    label: 'postnl.order.list.columnProduct',
                    allowResize: true,
                },
                {
                    property: 'customFields.postnl.barCode',
                    dataIndex: 'customFields.postnl.barCode',
                    label: 'postnl.order.list.columnBarCode',
                },
                {
                    property: 'customFields.postnl.confirm',
                    dataIndex: 'customFields.postnl.confirm',
                    label: 'postnl.order.list.columnConfirm',
                    align: 'center',
                },
                {
                    property: 'customFields.postnl.sentDate',
                    dataIndex: 'customFields.postnl.sentDate',
                    label: 'postnl.order.list.columnSentDate',
                    align: 'center',
                },{
                    property: 'customFields.postnl.deliveryDate',
                    dataIndex: 'customFields.postnl.deliveryDate',
                    label: 'postnl.order.list.columnDeliveryDate',
                    align: 'center',
                },
            ];

            extraColumns.forEach(column => {
                const key = (column.dataIndex ?? column.property).replace(/\.|,/g, '-');
                columnMap[key] = column;
            });

            // Hide the states by default
            columnMap['stateMachineState-name'].visible = false;
            columnMap['transactions-stateMachineState-name'].visible = false;
            columnMap['deliveries-stateMachineState-name'].visible = false;

            columnMap['orderNumber'].routerLink = 'postnl.order.detail';

            return [
                columnMap['orderDateTime'],
                columnMap['orderNumber'],
                columnMap['orderCustomer-lastName-orderCustomer-firstName'],
                columnMap['deliveries-shippingOrderAddress-street'],
                columnMap['customFields-postnl-productId'],
                columnMap['customFields-postnl-barCode'],
                columnMap['customFields-postnl-confirm'],
                columnMap['customFields-postnl-sentDate'],
                columnMap['customFields-postnl-deliveryDate'],
                columnMap['stateMachineState-name'],
                columnMap['transactions-stateMachineState-name'],
                columnMap['deliveries-stateMachineState-name'],
            ];
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
        },

        onChangeShipping() {
            new Promise(resolve => {
                this.isBulkChangeShippingModalOpen = false;
                this.isChangeShippingModalId = null;
                resolve();
            }).then(() => {
                this.onRefresh();
            });
        },

        onCreateShipment() {
            new Promise(resolve => {
                this.isBulkCreateShipmentModalOpen = false;
                this.isCreateShipmentModalId = null;
                resolve();
            }).then(() => {
                this.onRefresh();
            })
        }
    }
});
