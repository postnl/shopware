import template from './postnl-required-hint.html.twig';
import './postnl-required-hint.scss';

Shopware.Component.register('postnl-required-hint', {
    template,

    props: {
        textAlign: {
            type: 'String',
            required: false,
            default: 'left'
        }
    },

    computed: {
        classes() {
            return [
                `postnl-required-hint__align--${this.textAlign}`,
            ];
        },

    },
})
