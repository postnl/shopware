const { Component } = Shopware;

Component.extend('postnl-config-product-selection-pickup-nl-be-default', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'NL',
            destinationZone: 'BE',
            deliveryType: 'pickup',
        };
    },
})
