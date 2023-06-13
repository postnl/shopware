import template from './postnl-create-shipment-modal.html.twig';
// import './postnl-shipping-modal.scss';

// eslint-disable-next-line no-undef
const {Component, Mixin,} = Shopware;

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
        },

        hasActions(){
          return (this.confirmShipments || this.downloadLabels) && this.selectionHasProducts;
        },

        selectionHasProducts() {
            return !Object.values(this.selection)
                .map(order => order?.customFields?.postnl?.productId)
                .some(productId => [undefined, null, ""].includes(productId))
        }
    },


    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            console.log(this.selectionHasProducts);
        },

        closeModal() {
            if (!this.isProcessing) {
                this.$emit('close');
            }
        },

        sendShipments() {
            this.isProcessing = true;

            const orderIds = Object.values(this.selection).map(order => order.id);

            this.ShipmentService
                .generateBarcodes(orderIds)
                .catch(error => {
                    if (error.message) {
                        this.createNotificationError({
                            title: this.$tc('global.default.error'),
                            message: error.message,
                        });
                    }
                    return Promise.reject();
                })
                .then(() => this.ShipmentService.createShipments(
                    orderIds,
                    this.confirmShipments,
                    this.downloadLabels,
                ))
                .then(response => {
                    if (response.data && this.downloadLabels) {
                        //const filename = response.headers['content-disposition'].split('filename=')[1];
                        const link = document.createElement('a');
                        link.href = URL.createObjectURL(response.data);
                        //link.download = filename;
                        link.target = '_blank';
                        link.dispatchEvent(new MouseEvent('click'));
                        link.remove();
                    }
                    this.isSuccess = true;
                    if (this.confirmShipments) {
                        this.createNotificationSuccess({
                            title: this.$tc('global.default.success'),
                            message: this.$tc('postnl.order.modal.createShipments.confirmedShipments'),
                        });
                    }
                })
                .catch(() => {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: this.$tc('global.notification.unspecifiedSaveErrorMessage'),
                    });
                })
                .finally(() => {
                    this.isProcessing = false;
                    this.isSuccess = true;
                })
        },
        processFinish() {
            this.isSuccess = false;
            this.$emit('create-shipment');
        }
    },
});
