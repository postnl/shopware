import template from './postnl-create-return-modal.html.twig';

Shopware.Component.extend('postnl-create-return-modal', 'postnl-shipment-modal-base', {
    template,

    data() {
        return {
            returnType: null,
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

        returnTypes() {
            return {
                'smartReturn': true,
                'shipmentAndReturn': this.order?.customFields?.postnl?.returnOptions?.shipmentAndReturn === false
            }
        }
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
                .then(() => {
                    this.isProcessingSuccess = true

                    this.createNotificationSuccess({
                        title: this.$tc('global.default.success'),
                        message: this.$tc('postnl.order.modal.createReturn.success.smartReturn', this.orderIds.length),
                    })
                })
                .catch((errors) => {
                    [...new Set(errors.map(error => error.type))]
                        .map(type => [type, errors.filter(error => error.type === type)]) // Turns into an entry compatible array
                        .forEach(([type, errors]) =>
                            this.createNotificationError({
                                title: this.$tc('global.default.error'),
                                message: this.$tc(`postnl.order.modal.createReturn.errors.${type}`, errors.length, {
                                    count: errors.length,
                                    orderNumbers: errors.map(error => error.orderNumber).join(', '),
                                }),
                            })
                        )
                })
                .finally(() => {
                    this.isProcessing = false
                })
        },
    },
});
