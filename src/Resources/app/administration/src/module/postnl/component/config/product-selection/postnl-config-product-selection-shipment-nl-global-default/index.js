const { Component } = Shopware;

Component.extend('postnl-config-product-selection-shipment-nl-global-default', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'NL',
            destinationZone: 'GLOBAL',
            deliveryType: 'shipment',
        };
    },
})
