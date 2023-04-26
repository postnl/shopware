import template from './sw-order-delivery-metadata.html.twig'

Shopware.Component.override('sw-order-delivery-metadata', {
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
});
