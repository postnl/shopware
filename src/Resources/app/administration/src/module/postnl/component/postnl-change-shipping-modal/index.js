import template from './postnl-change-shipping-modal.html.twig';
// import './postnl-shipping-modal.scss';

import { object } from '../../../../core/service/util.service';

// eslint-disable-next-line no-undef
const {Component, Mixin, } = Shopware;

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
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if(!this.isBulk) {
                this.overrideProductId = Object.values(this.selection)[0].customFields?.postnl?.productId;
                this.isOverrideProduct = !!this.overrideProductId;
            }

            this.determineZones();
        },

        closeModal() {
            if(!this.isProcessing) {
                this.$emit('close');
            }
        },

        determineZones() {
            this.ShipmentService
                .determineDestinationZones(Object.values(object.map(this.selection, 'id')))
                .then(response => this.deliveryZones = response.zones);
        },

        sendShipments() {
            this.isProcessing = true;

            const orderIds = Object.values(this.selection).map(order => order.id);

            // this.ShipmentService
            //     .generateBarcodes(orderIds)
            //     .then(() => this.ShipmentService.createShipments(
            //         orderIds,
            //         this.isOverrideProduct,
            //         this.overrideProductId,
            //         this.confirmShipments,
            //         this.downloadLabels,
            //     ))
            //     .then(response => {
            //         if (response.data) {
            //             const filename = response.headers['content-disposition'].split('filename=')[1];
            //             const link = document.createElement('a');
            //             link.href = URL.createObjectURL(response.data);
            //             link.download = filename;
            //             link.dispatchEvent(new MouseEvent('click'));
            //             link.remove();
            //         }
            //     })
            //     .finally(() => {
            //         this.isProcessing = false;
            //         this.shipmentsSent = true;
            //     })
        }
    },
});
