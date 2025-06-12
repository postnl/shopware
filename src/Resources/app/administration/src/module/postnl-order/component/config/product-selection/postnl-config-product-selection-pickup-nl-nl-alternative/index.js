const { Component } = Shopware;

Component.extend('postnl-config-product-selection-pickup-nl-nl-alternative', 'postnl-config-product-selection-base', {

    data() {
        return {
            sourceZone: 'NL',
            destinationZone: 'NL',
            deliveryType: 'pickup',

            isAlt: true,
        };
    },
})
