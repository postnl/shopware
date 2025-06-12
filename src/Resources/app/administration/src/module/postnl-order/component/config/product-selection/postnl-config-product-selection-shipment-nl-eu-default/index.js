const { Component } = Shopware;

Component.extend('postnl-config-product-selection-shipment-nl-eu-default', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'NL',
            destinationZone: 'EU',
            deliveryType: 'shipment',
        };
    },
})
