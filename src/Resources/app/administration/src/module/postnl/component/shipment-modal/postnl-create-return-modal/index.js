import template from './postnl-create-return-modal.html.twig';

Shopware.Component.extend('postnl-create-return-modal', 'postnl-shipment-modal-base', {
    template,

    data() {
        return {
        };
    },

    computed: {
        modalClasses() {
            return [
                'postnl-create-return-modal'
            ]
        },

        modalTitle() {
            return this.$t('postnl.order.modal.createReturn.title')
        },

        isProcessingDisabled() {
            return false
        },
    },

    methods: {
        createdComponent() {
        },

        onStartProcessing() {
            this.isProcessing = true

            this.ShipmentService
                .createSmartReturn({
                    orderIds: this.orderIds,
                })
                .then(response => {
                    if (response.data) {
                        //const filename = response.headers['content-disposition'].split('filename=')[1]
                        const link = document.createElement('a')
                        link.href = URL.createObjectURL(response.data)
                        //link.download = filename
                        link.target = '_blank'
                        link.dispatchEvent(new MouseEvent('click'))
                        link.remove()
                    }

                    this.isProcessingSuccess = true

                    this.createNotificationSuccess({
                        title: this.$tc('global.default.success'),
                        message: this.$tc('postnl.order.modal.createShipments.confirmedShipments'),
                    })

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
});
