import './postnl-button.scss';

// eslint-disable-next-line no-undef
const {Component } = Shopware;

Component.extend('postnl-button', 'sw-button', {
    computed: {
        buttonClasses() {
            const baseClasses = this.$super('buttonClasses');

            baseClasses['postnl-button'] = true;

            return baseClasses;
        },

    },
});
