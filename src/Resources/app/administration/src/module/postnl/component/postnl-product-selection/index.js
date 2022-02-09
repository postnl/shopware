import template from './postnl-product-selection.html.twig';

const { Component } = Shopware;

Component.register('postnl-product-selection', {
    template,

    inject: [
        'ProductSelectionService'
    ],

    props: {
        value: {
            required: false,
        },
        sourceZone: {
            type: String,
            required: false,
            default: 'NL',
            validator: function (value) {
                // The value must match one of these strings
                return ['NL', 'BE'].indexOf(value) !== -1;
            },
        },
        destinationZone: {
            type: String,
            required: false,
            default: 'NL',
            validator: function (value) {
                // The value must match one of these strings
                return ['NL', 'BE', 'EU', 'GLOBAL'].indexOf(value) !== -1;
            },
        },
        deliveryType: {
            type: String,
            required: false,
            default: 'shipment',
            validator: function (value) {
                // The value must match one of these strings
                return ['mailbox', 'shipment', 'pickup'].indexOf(value) !== -1;
            },
        },
    },

    data() {
        return {
            isLoading: true,
            product: {},
            productAvailable: false,
            availableDeliveryTypes: [],
            availableOptions: [],

            internalSourceZone: null,
            internalDestinationZone: null,
            internalDeliveryType: null,
        }
    },

    computed: {
        actualSourceZone: {
            get() {
                return this.internalSourceZone;
            },
            set(value) {
                this.internalSourceZone = value;
                this.$emit('sourceZoneChanged', value);
            }
        },

        actualDestinationZone: {
            get() {
                return this.internalDestinationZone;
            },
            set(value) {
                this.internalDestinationZone = value;
                this.$emit('destinationZoneChanged', value);
            }
        },

        actualDeliveryType: {
            get() {
                return this.internalDeliveryType;
            },
            set(value) {
                this.internalDeliveryType = value;
                this.$emit('deliveryTypeChanged', value);
            }
        },
    },

    watch: {
        value: {
            handler(value) {
                this.$emit('input', value);
            },
            deep: true
        },
        sourceZone() {
            this.initInternalData();
            this.onChangeSourceZone();
        },
        destinationZone() {
            this.initInternalData();
            this.onChangeSourceZone();
        },
        deliveryType() {
            this.initInternalData();
            this.onChangeSourceZone();
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initInternalData();
            this.onChangeSourceZone();
        },

        initInternalData() {
            this.internalSourceZone = this.sourceZone;
            this.internalDestinationZone = this.destinationZone;
            this.internalDeliveryType = this.deliveryType;
        },

        onChangeSourceZone() {
            this.checkIfSourceZoneHasProducts()
                .then(this.getAvailableDeliveryTypes);
        },

        checkIfSourceZoneHasProducts() {
            return this.ProductSelectionService
                .sourceZoneHasProducts(this.actualSourceZone)
                .then(result => this.productsAvailable = result.hasProducts)
                .catch(() => {
                    this.productsAvailable = false;
                    this.actualDeliveryType = null;
                    this.availableDeliveryTypes = [];
                });
        },

        getAvailableDeliveryTypes() {
            return this.ProductSelectionService
                .getAvailableDeliveryTypes(this.actualSourceZone, this.actualDestinationZone)
                .then(deliveryTypes => {
                    this.availableDeliveryTypes = deliveryTypes.map(deliveryType => {
                        return {
                            label: deliveryType + " label",
                            value: deliveryType
                        };
                    });
                    this.actualDeliveryType = this.availableDeliveryTypes[0].value;
                });
        }
    }
})
