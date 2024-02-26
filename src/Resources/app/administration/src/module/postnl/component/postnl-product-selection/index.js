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
            default: ""
        },
        disabled: {
            type: Boolean,
            required: false,
            default: false,
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
                return ['shipment', 'pickup', 'mailbox', 'parcel'].indexOf(value) !== -1;
            },
        },
        showDeliveryType: {
            type: Boolean,
            required: false,
            default: true,
        },
        showProductCode: {
            type: Boolean,
            required: false,
            default: false,
        }
    },

    data() {
        return {
            isLoading: true,

            product: {},
            productId: "",
            productAvailable: false,

            deliveryTypes: [],
            internalDeliveryType: null,

            changeSet: [],
            flags: [],
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

                this.onChangeProduct();
            },
        },

        productId: {
            handler(value) {
                if (this.value !== value) {
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
                .then(this.getDeliveryTypes)
                .then(this.onChangeDeliveryType);
        },

        onChangeDeliveryType() {
            return this.getAvailableFlags()
                .then(() => {
                    if (!this.hasProduct && this.productId) {
                        return this.getProduct();
                    }

                    if (!this.hasProduct || this.product.deliveryType !== this.actualDeliveryType) {
                        return this.getDefaultProduct();
                    }
                })
                .then(this.buildInitialChangeSet)
                .then(product => this.product = product)
                .then(product => this.actualDeliveryType = product.deliveryType);
        },

        onChangeFlag(name) {
            this.setFlagChanged(name);

            // Select product based on selected flags
            return this.getProductBySelection();
        },

        onChangeProduct() {
            this.isLoading = true;
            return this.ProductSelectionService
                .getFlagsForProduct(this.productId)
                .then(this.updateFlags)
                .finally(() => {
                    this.isLoading = false;
                })
        },

        checkIfSourceZoneHasProducts() {
            this.isLoading = true;

            return this.ProductSelectionService
                .sourceZoneHasProducts(this.sourceZone)
                .then(result => this.productsAvailable = result.hasProducts)
                .catch(() => {
                    this.productsAvailable = false;
                    this.actualDeliveryType = null;
                    this.deliveryTypes = [];
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        getDeliveryTypes() {
            this.isLoading = true;

            return this.ProductSelectionService
                .getDeliveryTypes(this.sourceZone, this.destinationZone)
                .then(deliveryTypes => {
                    this.deliveryTypes = deliveryTypes.map(deliveryType => {
                        return {
                            label: this.$tc('postnl.productSelection.deliveryType.' + deliveryType),
                            value: deliveryType
                        };
                    });
                    if (!this.deliveryTypes.some(deliveryType => deliveryType.value === this.actualDeliveryType)) {
                        this.actualDeliveryType = this.deliveryTypes[0].value;
                    }
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
                    this.changeSet = [];
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        updateFlags(result) {
            this.flags = result.flags;
            return Promise.resolve(this.flags);
        },

        getProduct() {
            this.isLoading = true;

            return this.ProductSelectionService
                .getProduct(this.productId)
                .then(result => result.product)
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
                .then(result => result.product)
                .finally(() => {
                    this.isLoading = false;
                })
        },

        getProductBySelection() {
            this.isLoading = true;

            const flags = object.map(this.flags, 'selected');

            return this.ProductSelectionService
                .selectProduct(
                    this.sourceZone,
                    this.destinationZone,
                    this.actualDeliveryType,
                    flags,
                    this.changeSet
                )
                .then(result => result.product)
                .catch(this.getDefaultProduct)
                .then(product => this.product = product)
                .finally(() => {
                    this.isLoading = false;
                })
        },

        buildInitialChangeSet(product) {
            for (const name in this.flags) {
                if (this.flags[name].visible) {
                    if (product[name]) {
                        this.setFlagChanged(name, product[name]);
                    }
                }
            }
            return Promise.resolve(product);
        },

        setFlagChanged(name, value = null) {
            this.changeSet = this.changeSet.filter(flag => {
                return flag.name !== name;
            });

            this.changeSet.unshift({
                name: name,
                selected: value ?? this.flags[name].selected
            });
        },

        getFlagLabel(flag) {
            if (flag) {
                return this.$tc('postnl.productSelection.flag.' + flag.name);
            }
            return '';
        }
    }
})
