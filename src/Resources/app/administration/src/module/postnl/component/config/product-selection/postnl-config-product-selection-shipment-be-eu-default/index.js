const { Component } = Shopware;

Component.extend('postnl-config-product-selection-shipment-be-eu-default', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'BE',
            destinationZone: 'EU',
            deliveryType: 'shipment',
        };
    },
})
