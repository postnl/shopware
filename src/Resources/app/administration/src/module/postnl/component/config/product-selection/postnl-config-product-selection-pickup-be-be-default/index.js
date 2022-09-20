const { Component } = Shopware;

Component.extend('postnl-config-product-selection-pickup-be-be-default', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'BE',
            destinationZone: 'BE',
            deliveryType: 'pickup',
        };
    },
})
