import template from './postnl-change-shipping-modal.html.twig';
// import './postnl-shipping-modal.scss';

// eslint-disable-next-line no-undef
const { Component, Mixin, } = Shopware;

Component.register('postnl-change-shipping-modal', {
    template,

    inject: [
        'ShipmentService',
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    props: {
        selection: {
            type: Object,
            required: true,
        }
    },

    data() {
        return {
            isLoading: false,
            isProcessing: false,
            isSuccess: false,

            sourceZones: [],
            destinationZones: [],

            isOverrideProduct: false,
            overrideProductId: null,
        };
    },

    computed: {
        isBulk() {
            return this.selectionCount > 1;
        },

        selectionCount() {
            return Object.values(this.selection).length;
        },

        canChangeProduct() {
            return this.sourceZones.length === 1 && this.destinationZones.length === 1;
        },

        orderIds() {
            return Object.values(this.selection).map(order => order.id)
        },
    },

    created() {
        this.createdComponent();
    },

    watch: {
        isSuccess(value) {
            if(value) {
                return
            }

            this.$emit('change-shipping')
        }
    },

    methods: {
        createdComponent() {
            if (!this.isBulk) {
                this.overrideProductId = Object.values(this.selection)[0].customFields?.postnl?.productId;
                this.isOverrideProduct = !!this.overrideProductId;
            }

            this.determineZones()
        },

        closeModal() {
            if (!this.isProcessing) {
                this.$emit('close');
            }
        },

        determineZones() {
            this.isLoading = true;

            return this.ShipmentService
                .determineZones(this.orderIds)
                .then(({ source, destination }) => {
                    this.sourceZones = source
                    this.destinationZones = destination
                })
                .finally(() => this.isLoading = false)
        },

        sendShipments() {
            this.isProcessing = true;

            this.ShipmentService
                .changeProducts(this.orderIds, this.overrideProductId)
                .finally(() => {
                    this.isProcessing = false;
                    this.isSuccess = true;
                })
        },
    },
});
