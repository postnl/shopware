import template from './postnl-product-selection.html.twig';
import { object } from '../../../../core/service/util.service';

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
            internalDeliveryType: null,

            flags: [],
            selectedFlags: [],
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
            this.getAvailableFlags()
                .then(() => {
                    if(!this.product || this.product.deliveryType !== this.actualDeliveryType) {
                        return this.getDefaultProduct();
                    }
                });
        },

        onChangeFlag(name) {
            this.setFlagSelected(name);

            // Select product based on selected flags
            this.getProductBySelection();
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

        getAvailableFlags() {
            return this.ProductSelectionService
                .getAvailableFlags(
                    this.sourceZone,
                    this.destinationZone,
                    this.actualDeliveryType
                )
                .then(this.updateFlags)
                .then(() => {
                    this.selectedFlags = [];
                });
        },

        getDefaultProduct() {
            return this.ProductSelectionService
                .getDefaultProduct(
                    this.sourceZone,
                    this.destinationZone,
                    this.actualDeliveryType
                )
                .then(result => this.product = result.product)
                .then(product => this.setProductFlags(product))
        },

        getProductBySelection() {
            const flags = object.map(this.flags, 'selected');

            this.ProductSelectionService
                .selectProduct(
                    this.sourceZone,
                    this.destinationZone,
                    this.actualDeliveryType,
                    flags,
                    this.selectedFlags
                )
                .then(result => this.product = result.product)
                .then(product => {
                    this.selectedFlags = [];
                    return product;
                })
                .then(product => this.setProductFlags(product))
                .then(product => this.ProductSelectionService.getFlagsForProduct(product.id))
                .then(this.updateFlags)
        },

        setProductFlags(product) {
            for(const name in this.flags) {
                if(this.flags[name].visible) {
                    this.flags[name].selected = product[name];

                    if(product[name]) {
                        this.setFlagSelected(name);
                    }
                }
            }
        },

        setFlagSelected(name) {
            this.selectedFlags = this.selectedFlags.filter(flag => {
                return flag.name !== name;
            });

            this.selectedFlags.push({
                name: name,
                selected: this.flags[name].selected
            });
        },

        updateFlags(result) {
            console.log(result);
            return Promise.resolve(result)
                .then(result => this.flags = result.flags)
        }
    }
})
