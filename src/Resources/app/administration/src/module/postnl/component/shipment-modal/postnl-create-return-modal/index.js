import template from './postnl-create-return-modal.html.twig';
import './postnl-create-return-modal.scss';

Shopware.Component.extend('postnl-create-return-modal', 'postnl-shipment-modal-base', {
    template,

    data() {
        return {
            returnType: null,
            variant: 'large',
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

        labelInTheBoxAvailable() {
            return Object.values(this.selection)
                .filter(order => ![undefined, null].includes(order?.customFields?.postnl?.returnOptions?.labelInTheBox))
        },

        shipmentAndReturnAvailable() {
            return Object.values(this.selection)
                .filter(order => ![undefined, null].includes(order?.customFields?.postnl?.returnOptions?.shipmentAndReturn))
        },

        shipmentAndReturnEnabled() {
            return Object.values(this.selection)
                .filter(order => [false].includes(order?.customFields?.postnl?.returnOptions?.shipmentAndReturn))
        },

        returnTypes() {
            return [
                {
                    label: this.$t('postnl.order.modal.createReturn.type.smartReturn.label'),
                    value: 'smartReturn',
                    enabled: true,
                    icon: 'regular-truck',
                    description: this.$t('postnl.order.modal.createReturn.type.smartReturn.description'),
                },
                {
                    label: this.$t('postnl.order.modal.createReturn.type.shipmentAndReturn.label'),
                    value: 'shipmentAndReturn',
                    enabled: this.shipmentAndReturnEnabled.length > 0,
                    icon: 'regular-checkmark',
                    description: this.shipmentAndReturnAvailable.length > 0
                        ? this.$t('postnl.order.modal.createReturn.type.shipmentAndReturn.description')
                        : this.$t('postnl.order.modal.createReturn.type.notAvailable'),
                },
                {
                    label: this.$t('postnl.order.modal.createReturn.type.labelInTheBox.label'),
                    value: 'labelInTheBox',
                    enabled: false,
                    icon: 'regular-box',
                    description: this.labelInTheBoxAvailable.length > 0
                        ? this.$t('postnl.order.modal.createReturn.type.labelInTheBox.description')
                        : this.$t('postnl.order.modal.createReturn.type.notAvailable'),
                }
            ]
        }
    },

    methods: {
        createdComponent() {
        },

        onStartProcessing() {

        },

        createSmartReturn() {
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
                                message: this.$tc(`postnl.order.modal.createReturn.errors.${ type }`, errors.length, {
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
