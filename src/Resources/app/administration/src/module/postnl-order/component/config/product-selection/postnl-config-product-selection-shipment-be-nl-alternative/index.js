const { Component } = Shopware;

Component.extend('postnl-config-product-selection-shipment-be-nl-alternative', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'BE',
            destinationZone: 'NL',
            deliveryType: 'shipment',

            isAlt: true,
        };
    },
})
