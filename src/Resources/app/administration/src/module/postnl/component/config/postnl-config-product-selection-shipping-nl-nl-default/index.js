const { Component } = Shopware;

Component.extend('postnl-config-product-selection-shipping-nl-nl-default', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'NL',
            destinationZone: 'NL',
            deliveryType: 'shipment',
        };
    },
})
