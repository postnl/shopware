import template from './postnl-create-return-modal.html.twig';
import './postnl-create-return-modal.scss';

const { Criteria } = Shopware.Data

Shopware.Component.extend('postnl-create-return-modal', 'postnl-shipment-modal-base', {
    template,

    mixins: [
        'placeholder'
    ],

    data() {
        return {
            returnType: null,
            variant: 'large',
            smartReturnMailTemplate: null
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
            return this.returnType === null || (this.returnType === 'smartReturn' && this.smartReturnMailTemplate === null) || this.hasMultipleZones
        },

        hasMultipleZones() {
            return this.destinationZones.length > 1
        },

        mailTemplateCriteria() {
            const criteria = new Criteria()
            criteria.addAssociation('mailTemplateType')
            criteria.addFilter(Criteria.equals('mailTemplateType.technicalName', 'postnl_return_mail'))

            return criteria
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
                    requiredZones: ['NL'],
                },
                {
                    label: this.$t('postnl.order.modal.createReturn.type.shipmentAndReturn.label'),
                    value: 'shipmentAndReturn',
                    enabled: this.shipmentAndReturnEnabled.length > 0,
                    icon: 'regular-checkmark',
                    description: this.shipmentAndReturnAvailable.length > 0
                        ? this.$t('postnl.order.modal.createReturn.type.shipmentAndReturn.description')
                        : this.$t('postnl.order.modal.createReturn.type.notAvailable'),
                    requiredZones: ['NL'],
                },
                {
                    label: this.$t('postnl.order.modal.createReturn.type.labelInTheBox.label'),
                    value: 'labelInTheBox',
                    enabled: false,
                    icon: 'regular-box',
                    description: this.labelInTheBoxAvailable.length > 0
                        ? this.$t('postnl.order.modal.createReturn.type.labelInTheBox.description')
                        : this.$t('postnl.order.modal.createReturn.type.notAvailable'),
                    requiredZones: ['NL', 'BE'],
                }
            ]
                .filter(returnType => returnType.requiredZones.some(zone => this.destinationZones.includes(zone)))
        }
    },

    methods: {
        createdComponent() {
        },

        mailTemplateLabel(item) {
            return [
                this.placeholder(item?.mailTemplateType, 'name', 'PostNL Return mail'),
                this.placeholder(item, 'description', ''),
                this.placeholder(item, 'subject'),
            ]
                .filter(string => string && string.length > 0)
                .join(' - ')
        },

        onStartProcessing() {
            switch (this.returnType) {
                case 'smartReturn':
                    this.createSmartReturn()
                    break;
                case 'labelInTheBox':
                    this.isProcessingSuccess = true;
                    break;
                case 'shipmentAndReturn':
                    //TODO implement activation
                    this.activateReturnlabels()
                    break;
                default:
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: this.$t('global.default.error'),
                    })
            }
        },

        updateReturnType(type) {
            this.returnType = type
        },

        activateReturnlabels() {
            this.isProcessing = true

            this.ShipmentService
                .activateReturnLabels({
                    orderIds: this.orderIds
                })
                .then((response) => {
                    this.isProcessingSuccess = true

                    if(response.successfulBarcodes.length > 0) {
                        this.createNotificationSuccess({
                            title: this.$tc('global.default.success'),
                            message: this.$tc('postnl.order.modal.createReturn.success.shipmentAndReturn', response.successfulBarcodes.length, {
                                count: response.successfulBarcodes.length
                            }),
                        })
                    }

                    if(response.errorsPerBarcode.length > 0) {
                        response.errorsPerBarcode.forEach(barcodeError => {
                            if(barcodeError.warnings.length > 0) {
                                barcodeError.warnings.forEach(warning => {
                                    this.createNotificationWarning({
                                        title: `${this.$tc('global.default.warning')} ${barcodeError.barcode}`,
                                        message: warning.description,
                                    })
                                })
                            }

                            if(barcodeError.errors.length > 0) {
                                barcodeError.errors.forEach(error => {
                                    this.createNotificationError({
                                        title: `${this.$tc('global.default.error')} ${barcodeError.barcode}`,
                                        message: error.description
                                    })
                                })
                            }
                        })
                    }
                })
                .catch(() => {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: this.$tc('sw-error.general.messagePlaceholder')
                    })
                })
                .finally(() => {
                    this.isProcessing = false
                })
        },

        createSmartReturn() {
            this.isProcessing = true

            this.ShipmentService
                .createSmartReturn({
                    orderIds: this.orderIds,
                    mailTemplateId: this.smartReturnMailTemplate
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
