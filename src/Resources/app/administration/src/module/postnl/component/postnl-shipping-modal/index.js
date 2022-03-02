import template from './postnl-shipping-modal.html.twig';
// import './postnl-shipping-modal.scss';

// eslint-disable-next-line no-undef
const {Component, Mixin, } = Shopware;

Component.register('postnl-shipping-modal', {
    template,

    inject: [
        'ShipmentService'
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

            isOverrideProduct: false,
            overrideProductId: null,

            confirmShipments: false,
        };
    },

    computed: {
        isBulk() {
            return Object.values(this.selection).length > 1;
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if(!this.isBulk) {
                this.overrideProductId = Object.values(this.selection)[0].customFields?.postnl_shipments?.productId;
                this.isOverrideProduct = !!this.overrideProductId;
            }
        },

        closeModal() {
            if(!this.isProcessing) {
                this.$emit('close');
            }
        },

        sendShipments() {
            this.isProcessing = true;

            const orderIds = Object.values(this.selection).map(order => order.id);

            this.ShipmentService
                .generateBarcodes(orderIds)
                .then(() => this.ShipmentService.createShipments(orderIds, this.isOverrideProduct, this.overrideProductId))
                .then(response => {
                    if (response.data) {
                        const filename = response.headers['content-disposition'].split('filename=')[1];
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(response.data);
                        link.download = filename;
                        link.dispatchEvent(new MouseEvent('click'));
                        link.remove();
                    }
                })
                .finally(() => {
                    this.isProcessing = false;
                })
        }
    },
});