import template from './postnl-product-selection.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-product-selection', 'memo-config', {
    template,

    mixins: [
        Mixin.getByName('memo-config-access'),
    ],

    inject: [
        'ProductSelectionService'
    ],

    data() {
        return {
            isLoading: true,
            product: null,
            availableOptions: [],
        }
    },

    computed: {
        defaultValues() {
            return {
                sourceZone: 'NL',
                destinationZone: 'NL',
                deliveryType: 'shipment',
                nextDoorDelivery: true,
                returnIfNotHome: false,
                insurance: false,
                signature: false,
                ageCheck: false,
                notification: false,
            };
        },

        deliveryTypeOptions() {
            return [
                {
                    label: "Shipment",
                    value: "shipment",
                },
                {
                    label: "Pickup Point",
                    value: "pickup",
                },
                {
                    label: "Mailbox",
                    value: "mailbox",
                },
            ];
        },

        deliveryType: {
            get() {
                return this.resolveValue('deliveryType');
            },
            set(value) {
                console.log(value);
                this.content.deliveryType = value;
                this.loadOptions();
            }
        },
        nextDoorDelivery: {
            get() {
                return this.resolveValue('nextDoorDelivery');
            },
            set(value) {
                console.log(value);
                this.content.nextDoorDelivery = value;
                this.loadOptions();
            }
        },
        returnIfNotHome: {
            get() {
                return this.resolveValue('returnIfNotHome');
            },
            set(value) {
                console.log(value);
                this.content.returnIfNotHome = value;
                this.loadOptions();
            }
        },
        insurance: {
            get() {
                return this.resolveValue('insurance');
            },
            set(value) {
                console.log(value);
                this.content.insurance = value;
                this.loadOptions();
            }
        },
        signature: {
            get() {
                return this.resolveValue('signature');
            },
            set(value) {
                console.log(value);
                this.content.signature = value;
                this.loadOptions();
            }
        },
        ageCheck: {
            get() {
                return this.resolveValue('ageCheck');
            },
            set(value) {
                console.log(value);
                this.content.ageCheck = value;
                this.loadOptions();
            }
        },
        notification: {
            get() {
                return this.resolveValue('notification');
            },
            set(value) {
                console.log(value);
                this.content.notification = value;
                this.loadOptions();
            }
        },
    },

    watch: {
        // content: {
        //     handler() {
        //         // this.ProductSelectionService.select()
        //         this.loadProduct();
        //     },
        //     deep: true,
        // }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.loadOptions();
        },

        loadOptions() {
            this.ProductSelectionService
                .options(
                    this.resolveValue('sourceZone'),
                    this.resolveValue('destinationZone'),
                    this.resolveValue('deliveryType')
                )
                .then(result => {
                    this.availableOptions = result.options
                })
                .then(this.loadProduct);
        },

        loadProduct() {
            this.ProductSelectionService
                .select(
                    this.resolveValue('sourceZone'),
                    this.resolveValue('destinationZone'),
                    this.resolveValue('deliveryType'),
                    {
                        nextDoorDelivery: this.resolveOptionValue('nextDoorDelivery'),
                        returnIfNotHome: this.resolveOptionValue('returnIfNotHome'),
                        insurance: this.resolveOptionValue('insurance'),
                        signature: this.resolveOptionValue('signature'),
                        ageCheck: this.resolveOptionValue('ageCheck'),
                        notification: this.resolveOptionValue('notification'),
                    }
                )
                .then(result => {
                    this.product = result.product;
                    this.availableOptions = result.options;
                })
        },

        resolveValue(key) {
            const value = this.content[key] ?? this.defaultValues[key] ?? null;
            console.log(`Resolved ${ key } to ${ value }`);
            return value;
        },

        resolveOptionValue(key) {
            const value = this.content[key] ?? this.availableOptions[key].selected ?? this.defaultValues[key] ?? null;
            console.log(`Resolved ${ key } to ${ value }`);
            return value;
        }
    }

})
