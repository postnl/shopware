import template from "./postnl-shipment-modal-base.html.twig"

const { Component, Mixin } = Shopware

Component.register('postnl-shipment-modal-base', {
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
            variant: 'default'
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

        onStartProcessing() {
        },

        onProcessSuccess() {
        },

        onProcessSuccessEnd() {
            this.closeModal()
            location.reload()
        },
    },
})