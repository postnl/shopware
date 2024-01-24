import template from './sw-order-detail-details.html.twig'

Shopware.Component.override('sw-order-detail-details', {
    template,

    computed: {
        isPostNLOrder() {
            return this.order.customFields !== null && 'postnl' in this.order.customFields;
        },

        postNLDeliveryDate() {
            if(!this.isPostNLOrder) {
                return null;
            }

            return this.order.customFields.postnl?.deliveryDate || null;
        },

        postNLSentDate() {
            if(!this.isPostNLOrder) {
                return null;
            }

            return this.order.customFields.postnl?.sentDate || null;
        }
    },
})