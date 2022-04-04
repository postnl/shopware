import './postnl-button.scss';

// eslint-disable-next-line no-undef
const {Component } = Shopware;

Component.extend('postnl-button', 'sw-button', {
    computed: {
        buttonClasses() {
            const baseClasses = this.$super('buttonClasses');

            baseClasses['postnl-button'] = true;

            return baseClasses;

            return {
                [`sw-button--${this.variant}`]: this.variant,
                [`sw-button--${this.size}`]: this.size,
                'sw-button--block': this.block,
                'sw-button--disabled': this.disabled,
                'sw-button--square': this.square,
            };
        },

    },
});
