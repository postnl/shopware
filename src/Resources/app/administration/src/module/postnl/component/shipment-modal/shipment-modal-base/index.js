import template from "./shipment-modal-base.html.twig";

const { Component, Mixin } = Shopware;

Component.register('postnl-shipment-modal-base', {
    template,

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
        };
    },

    computed: {
        modalClasses() {
            return {}
        },

        modalTitle() {
            return ''
        },

        isBulk() {
            return this.selectionCount > 1;
        },

        isProcessingDisabled() {
            return false
        },

        selectionCount() {
            return Object.values(this.selection).length;
        },

        selectionIsMissingProduct() {
            return Object.values(this.selection)
                .map(order => order?.customFields?.postnl?.productId)
                .some(productId => [undefined, null, ""].includes(productId))
        },

        orderIds() {
            return Object.values(this.selection).map(order => order.id)
        },
    },

    created() {
        this.createdComponent();
    },

    watch: {
        isSuccess(value) {
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
                this.$emit('close');
            }
        },

        onStartProcessing() {
        },
        onProcessSuccess() {
        },
        onProcessSuccessEnd() {
        },
    },
})