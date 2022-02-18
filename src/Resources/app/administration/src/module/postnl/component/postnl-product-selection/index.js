import template from './postnl-product-selection.html.twig';
import './postnl-product-selection.scss';

import { object } from '../../../../core/service/util.service';

const { Component } = Shopware;

Component.register('postnl-product-selection', {
    template,

    inject: [
        'ProductSelectionService'
    ],

    props: {
        // productId
        value: {
            required: false,
            default: "6aa1d2225d724416bea415e2454de832"
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
        showDeliveryType: {
            type: Boolean,
            required: false,
            default: true,
        },
    },

    data() {
        return {
            isLoading: true,

            product: {},
            productId: "",
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

        hasProduct() {
            return this.product && this.product.hasOwnProperty('id');
        }
    },

    watch: {
        value: {
            handler(value) {
                this.productId = value ?? "";
            },
            immediate: true,
        },

        product: {
            handler(product) {
                this.productId = product.id;
            },
        },

        productId: {
            handler(value) {
                if (value !== this.productId) {
                    this.$emit('input', value);
                }
            }
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
            return this.checkIfSourceZoneHasProducts()
                .then(this.getAvailableDeliveryTypes)
                .then(this.onChangeDeliveryType);
        },

        onChangeDeliveryType() {
            return this.getAvailableFlags()
                .then(async () => {
                    if (!this.hasProduct && this.productId) {
                        await this.getProduct();
                    }

                    if (!this.hasProduct || this.product.deliveryType !== this.actualDeliveryType) {
                        return this.getDefaultProduct();
                    }
                });
        },

        onChangeFlag(name) {
            this.setFlagSelected(name);

            // Select product based on selected flags
            return this.getProductBySelection();
        },

        checkIfSourceZoneHasProducts() {
            this.isLoading = true;

            return this.ProductSelectionService
                .sourceZoneHasProducts(this.sourceZone)
                .then(result => this.productsAvailable = result.hasProducts)
                .catch(() => {
                    this.productsAvailable = false;
                    this.actualDeliveryType = null;
                    this.availableDeliveryTypes = [];
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        getAvailableDeliveryTypes() {
            this.isLoading = true;

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
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        getAvailableFlags() {
            this.isLoading = true;

            return this.ProductSelectionService
                .getAvailableFlags(
                    this.sourceZone,
                    this.destinationZone,
                    this.actualDeliveryType
                )
                .then(this.updateFlags)
                .then(() => {
                    this.selectedFlags = [];
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        getProduct() {
            this.isLoading = true;

            return this.ProductSelectionService
                .getProduct(this.productId)
                .then(result => this.product = result.product)
                .then(product => this.setProductFlags(product))
                .finally(() => {
                    this.isLoading = false;
                })
        },

        getDefaultProduct() {
            this.isLoading = true;

            return this.ProductSelectionService
                .getDefaultProduct(
                    this.sourceZone,
                    this.destinationZone,
                    this.actualDeliveryType
                )
                .then(result => this.product = result.product)
                .then(product => this.setProductFlags(product))
                .finally(() => {
                    this.isLoading = false;
                })
        },

        getProductBySelection() {
            this.isLoading = true;

            const flags = object.map(this.flags, 'selected');

            /**
             * Needs to change
             * On selection, run getFlagsForProductSelection using currentFlags and changeSet,
             * then set the here, and based on flag state then get the correct product.
              */
            this.ProductSelectionService
                .selectProduct(
                    this.sourceZone,
                    this.destinationZone,
                    this.actualDeliveryType,
                    flags,
                    this.selectedFlags
                )
                .then(result => this.product = result.product)
                .then(product => this.setProductFlags(product))//?
                .then(product => this.ProductSelectionService.getFlagsForProduct(product.id))
                .then(this.updateFlags)
                .then(flags => this.updateFlagSelection(flags))
                .finally(() => {
                    this.isLoading = false;
                })
        },

        setProductFlags(product) {
            for (const name in this.flags) {
                if (this.flags[name].visible) {
                    this.flags[name].selected = product[name];

                    if (product[name]) {
                        this.setFlagSelected(name);
                    }
                }
            }
            return product;
        },

        setFlagSelected(name) {
            this.selectedFlags = this.selectedFlags.filter(flag => {
                return flag.name !== name;
            });

            this.selectedFlags.unshift({
                name: name,
                selected: this.flags[name].selected
            });
        },

        updateFlags(result) {
            console.log(result);
            return Promise.resolve(result)
                .then(result => this.flags = result.flags);
        },

        updateFlagSelection(flags) {
            for (const key in this.selectedFlags) {
                this.selectedFlags[key].selected = flags[this.selectedFlags[key].name].selected;
            }

            this.selectedFlags = this.selectedFlags.filter(flag => {
                return flag.selected;
            });
        },
    }
})
