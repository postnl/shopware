const { Component } = Shopware;

Component.extend('postnl-config-product-selection-shipment-nl-be-default', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'NL',
            destinationZone: 'BE',
            deliveryType: 'shipment',
        };
    },
})
