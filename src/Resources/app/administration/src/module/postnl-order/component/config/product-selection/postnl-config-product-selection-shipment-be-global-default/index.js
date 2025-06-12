const { Component } = Shopware;

Component.extend('postnl-config-product-selection-shipment-be-global-default', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'BE',
            destinationZone: 'GLOBAL',
            deliveryType: 'shipment',
        };
    },
})
