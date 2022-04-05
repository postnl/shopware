import template from './postnl-create-shipment-modal.html.twig';
// import './postnl-shipping-modal.scss';

import { object } from '../../../../core/service/util.service';

// eslint-disable-next-line no-undef
const {Component, Mixin, } = Shopware;

Component.register('postnl-create-shipment-modal', {
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

            confirmShipments: true,
            downloadLabels: true,
        };
    },

    computed: {
        isBulk() {
            return this.selectionCount > 1;
        },

        selectionCount() {
            return Object.values(this.selection).length;
        }
    },

    methods: {
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
                .catch(error => {
                    if(error.message) {
                        this.createNotificationError({
                            title: this.$tc('global.default.error'),
                            message: error.message,
                        });
                    }
                })
                .then(() => this.ShipmentService.createShipments(
                    orderIds,
                    this.confirmShipments,
                    this.downloadLabels,
                ))
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
                    this.shipmentsSent = true;
                })
        }
    },
});
