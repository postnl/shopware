import template from './postnl-create-return-modal.html.twig';
import './postnl-create-return-modal.scss';

const { Criteria } = Shopware.Data

Shopware.Component.extend('postnl-create-return-modal', 'postnl-shipment-modal-base', {
    template,

    inject: [
        'systemConfigApiService'
    ],

    mixins: [
        'placeholder'
    ],

    data() {
        return {
            returnAddress: null,
            returnType: null,
            variant: 'large',
            smartReturnMailTemplate: null,
            shipmentAndReturnMailTemplate: null,
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
            return this.returnType === null ||
                (this.returnType === 'smartReturn' && this.smartReturnMailTemplate === null) ||
                (this.returnType === 'shipmentAndReturn' && this.shipmentAndReturnMailTemplate === null) ||
                this.hasMultipleZones
        },

        hasMultipleZones() {
            return this.destinationZones.length > 1
        },

        smartReturnMailTemplateCriteria() {
            const criteria = new Criteria()
            criteria.addAssociation('mailTemplateType')
            criteria.addFilter(Criteria.equals('mailTemplateType.technicalName', 'postnl_return_mail'))

            return criteria
        },

        smartReturnBeMailTemplateCriteria() {
            const criteria = new Criteria()
            criteria.addAssociation('mailTemplateType')
            criteria.addFilter(Criteria.equals('mailTemplateType.technicalName', 'postnl_return_mail_be'))

            return criteria
        },

        shipmentAndReturnMailTemplateCriteria() {
            const criteria = new Criteria()
            criteria.addAssociation('mailTemplateType')
            criteria.addFilter(Criteria.equals('mailTemplateType.technicalName', 'postnl_activate_shipment_and_return_label_mail'))

            return criteria
        },

        hasConfirmedOrders() {
            return Object.values(this.selection)
                .some(order => [true].includes(order?.customFields?.postnl?.confirm))
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
                    enabled: this.hasConfirmedOrders,
                    icon: 'regular-truck',
                    description: this.$t('postnl.order.modal.createReturn.type.smartReturn.description'),
                    requiredZones: ['NL', 'BE'],
                    returnZones: ['NL', 'BE'],
                },
                {
                    label: this.$t('postnl.order.modal.createReturn.type.shipmentAndReturn.label'),
                    value: 'shipmentAndReturn',
                    enabled: this.hasConfirmedOrders && this.shipmentAndReturnEnabled.length > 0,
                    icon: 'regular-checkmark',
                    description: this.shipmentAndReturnAvailable.length > 0
                        ? this.$t('postnl.order.modal.createReturn.type.shipmentAndReturn.description')
                        : this.$t('postnl.order.modal.createReturn.type.notAvailable'),
                    requiredZones: ['NL'],
                    returnZones: ['NL', 'BE'],
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
                    returnZones: ['NL'],
                }
            ]
                .filter(returnType => returnType.requiredZones.some(zone => this.destinationZones.includes(zone)))
                .filter(returnType => returnType.returnZones.some(zone => zone === this.returnZone))
        },

        returnZone() {
            return this?.returnAddress?.countrycode
        },
    },

    methods: {
        createdComponent() {
            this.getReturnAddress()
        },

        getReturnAddress() {
            this.systemConfigApiService
                .getValues('PostNLShopware')
                .then(config => config['PostNLShopware.config.returnAddress'])
                .then(string => JSON.parse(string))
                .then(address => this.returnAddress = address)
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
                    this.activateReturnLabels()
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

        activateReturnLabels() {
            this.isProcessing = true

            this.ShipmentService
                .activateReturnLabels({
                    orderIds: this.orderIds,
                    mailTemplateId: this.shipmentAndReturnMailTemplate
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
                                    message: errors[0].errorMessage // This will only show when there's 1 error.
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
