import template from './postnl-change-shipping-modal.html.twig';
// import './postnl-shipping-modal.scss';

// eslint-disable-next-line no-undef
const {Component, Mixin,} = Shopware;

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
            isProcessing: false,
            isSuccess: false,

            deliveryZones: [],

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
            return this.deliveryZones.length === 1;
        },

        orderIds() {
            return Object.values(this.selection).map(order => order.id)
        },
    },

    created() {
        this.createdComponent();
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
            return this.ShipmentService
                .determineZones(this.orderIds)
                .then(response => this.deliveryZones = response.zones);
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

        processFinish() {
            this.isSuccess = false;
            this.$emit('change-shipping');
        }
    },
});
