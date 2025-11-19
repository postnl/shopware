import template from './postnl-required-hint.html.twig';
import './postnl-required-hint.scss';

export default {
    template,

    props: {
        textAlign: {
            type: String,
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

    }
}
