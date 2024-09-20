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
        },
    },
});
