import template from "./postnl-shipment-modal-base.html.twig"

const { Mixin } = Shopware

export default {
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
            isLoading: false,
            isProcessing: false,
            isProcessingSuccess: false,
            variant: 'default',

            sourceZones: [],
            destinationZones: [],
        }
    },

    computed: {
        modalClasses() {
            return {}
        },

        modalTitle() {
            return ''
        },

        isBulk() {
            return this.selectionCount > 1
        },

        isProcessingDisabled() {
            return false
        },

        orderIds() {
            return Object.values(this.selection).map(order => order.id)
        },

        productIds() {
            return Object.values(this.selection).map(order => order?.customFields?.postnl?.productId)
        },

        selectionCount() {
            return Object.values(this.selection).length
        },

        selectionIsMissingProduct() {
            return this.productIds.some(productId => [undefined, null, ""].includes(productId))
        },
    },

    created() {
        this.createdComponent()
        this.determineZones()
    },

    watch: {
        isProcessingSuccess(value) {
            if (value === true) {
                this.onProcessSuccess()
                this.$emit('success-start')
                return
            }

            this.onProcessSuccessEnd()
            this.$emit('success-end')
        }
    },

    methods: {
        createdComponent() {
        },

        closeModal() {
            if (!this.isProcessing) {
                this.$emit('close')
            }
        },

        determineZones() {
            this.isLoading = true;

            return this.ShipmentService
                .determineZones(this.orderIds)
                .then(({ source, destination }) => {
                    this.sourceZones = source
                    this.destinationZones = destination
                })
                .finally(() => this.isLoading = false)
        },

        onStartProcessing() {
        },

        onProcessSuccess() {
        },

        onProcessSuccessEnd() {
            this.closeModal()
            location.reload()
        },
    },
}