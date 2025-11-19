import template from './postnl-change-shipping-modal.html.twig';

export default {
    template,

    data() {
        return {
            isOverrideProduct: false,
            overrideProductId: null,
        };
    },

    computed: {
        modalClasses() {
            return [
                'postnl-change-shipping-modal'
            ]
        },

        modalTitle() {
            return this.$t('postnl.order.modal.changeShipping.title')
        },

        isProcessingDisabled() {
            return this.sourceZones.length > 1 || this.destinationZones.length > 1;
        },
    },

    methods: {
        createdComponent() {
            if (!this.isBulk) {
                this.overrideProductId = this.productIds.shift();
                this.isOverrideProduct = !!this.overrideProductId;
            }
        },


        onStartProcessing() {
            this.isProcessing = true;

            this.ShipmentService
                .changeProducts(this.orderIds, this.overrideProductId)
                .finally(() => {
                    this.isProcessing = false;
                    this.isProcessingSuccess = true;
                })
        },
    },
}
