const { Component } = Shopware;

Component.extend('postnl-config-product-selection-shipment-nl-be-alternative', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'NL',
            destinationZone: 'BE',
            deliveryType: 'shipment',

            isAlt: true,
            defaultIsEnabled: false,
        };
    },
})
