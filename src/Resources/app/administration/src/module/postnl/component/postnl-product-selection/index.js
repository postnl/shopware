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
            availableFlags: [],
            flags: {},
            selectedFlags: [],

            internalDeliveryType: null,
        }
    },

    computed: {
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

        flags: {
            handler(value) {
                console.log(value);
            },
            deep: true
        },

        sourceZone() {
            this.initInternalData();
            this.onChangeSourceZone();
        },

        deliveryType() {
            this.initInternalData();
            this.onChangeDeliveryType();
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
            this.internalDeliveryType = this.deliveryType;
        },

        onChangeSourceZone() {
            this.checkIfSourceZoneHasProducts()
                .then(this.getAvailableDeliveryTypes)
                .then(this.onChangeDeliveryType);
        },

        onChangeDeliveryType() {
            this.getAvailableDeliveryOptions()
                //.then(this.getDefaultProduct);
        },

        onChangeFlag(name) {
            this.selectedFlags = this.selectedFlags.filter(flag => {
                return flag.name !== name;
            });

            this.selectedFlags.push({
                name: name,
                selected: this.flags[name]
            });
        },

        checkIfSourceZoneHasProducts() {
            return this.ProductSelectionService
                .sourceZoneHasProducts(this.sourceZone)
                .then(result => this.productsAvailable = result.hasProducts)
                .catch(() => {
                    this.productsAvailable = false;
                    this.actualDeliveryType = null;
                    this.availableDeliveryTypes = [];
                });
        },

        getAvailableDeliveryTypes() {
            return this.ProductSelectionService
                .getAvailableDeliveryTypes(this.sourceZone, this.destinationZone)
                .then(deliveryTypes => {
                    this.availableDeliveryTypes = deliveryTypes.map(deliveryType => {
                        return {
                            label: deliveryType + " label",
                            value: deliveryType
                        };
                    });
                    this.actualDeliveryType = this.availableDeliveryTypes[0].value;
                });
        },

        getAvailableDeliveryOptions() {
            return this.ProductSelectionService
                .getAvailableFlags(
                    this.sourceZone,
                    this.destinationZone,
                    this.actualDeliveryType
                )
                .then(result => this.availableFlags = result.flags)
                .then(flags => {
                    this.selectedFlags = [];
                    this.flags = {};
                    for(const name in flags) {
                        this.flags[name] = flags[name].selected;
                    }
                });
        }
    }
})
