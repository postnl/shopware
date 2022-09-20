const { Component } = Shopware;

Component.extend('postnl-config-product-selection-shipment-be-be-alternative', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'BE',
            destinationZone: 'BE',
            deliveryType: 'shipment',

            isAlt: true,
            defaultIsEnabled: false,
        };
    },
})
