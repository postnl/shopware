import template from './postnl-create-shipment-modal.html.twig';

// eslint-disable-next-line no-undef
const {Component, Mixin,} = Shopware;

Component.extend('postnl-create-shipment-modal', 'postnl-shipment-modal-base',{
    template,

    inject: [
        'ShipmentService',
    ],

    data() {
        return {
            confirmShipments: true,
            downloadLabels: true,
        };
    },

    computed: {
        modalClasses() {
            return [
                'postnl-create-shipment-modal'
            ]
        },

        modalTitle() {
            return this.$tc('postnl.order.modal.createShipments.title', this.selectionCount, {
                count: this.selectionCount
            })
        },

        isProcessingDisabled(){
            return (!this.confirmShipments && !this.downloadLabels) || this.selectionIsMissingProduct
        },
    },

    methods: {
        sendShipments() {
            this.isProcessing = true

            this.ShipmentService
                .generateBarcodes(this.orderIds)
                .catch(error => {
                    if (error.message) {
                        this.createNotificationError({
                            title: this.$tc('global.default.error'),
                            message: error.message,
                        });
                    }
                    return Promise.reject()
                })
                .then(() => this.ShipmentService.createShipments(
                    this.orderIds,
                    this.confirmShipments,
                    this.downloadLabels,
                ))
                .then(response => {
                    if (response.data && this.downloadLabels) {
                        //const filename = response.headers['content-disposition'].split('filename=')[1]
                        const link = document.createElement('a')
                        link.href = URL.createObjectURL(response.data)
                        //link.download = filename
                        link.target = '_blank'
                        link.dispatchEvent(new MouseEvent('click'))
                        link.remove()
                    }

                    this.isProcessingSuccess = true

                    if (this.confirmShipments) {
                        this.createNotificationSuccess({
                            title: this.$tc('global.default.success'),
                            message: this.$tc('postnl.order.modal.createShipments.confirmedShipments'),
                        })
                    }
                })
                .catch(() => {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: this.$tc('global.notification.unspecifiedSaveErrorMessage'),
                    })
                })
                .finally(() => {
                    this.isProcessing = false
                    this.isProcessingSuccess = true
                })
        },
    },
})
