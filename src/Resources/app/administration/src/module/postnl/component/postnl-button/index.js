import './postnl-button.scss';

// eslint-disable-next-line no-undef
const { Component } = Shopware;

// TODO Shopware 6.7: Create copy of mt-button instead, as that is a basic vue component and cannot be overridden
Component.extend(
    'postnl-button',
    Component.getComponentRegistry().has('sw-button-deprecated') ? 'sw-button-deprecated' : 'sw-button',
    {
        computed: {
            buttonClasses() {
                const baseClasses = this.$super('buttonClasses');

                baseClasses['postnl-button'] = true;

                return baseClasses;
            },
        },
    }
);
