import template from './postnl-button.html.twig'
import './postnl-button-new.scss'
import './sw-button-group.scss'

export default {
    template,

    inheritAttrs: false,

    props: {
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
        size: {
            type: String,
            required: false,
            default: '',
            validValues: [
                'x-small',
                'small',
            ],
            validator(value) {
                if (!value.length) {
                    return true;
                }

                return [
                    'x-small',
                    'small',
                ].includes(value);
            },
        },
        square: {
            type: Boolean,
            required: false,
            default: false,
        },
        block: {
            type: Boolean,
            required: false,
            default: false,
        },
        ghost: {
            type: Boolean,
            required: false,
            default: false,
        },
        // eslint-disable-next-line vue/require-default-prop
        routerLink: {
            type: Object,
            required: false,
        },
        link: {
            type: String,
            required: false,
            default: null,
        },
        isLoading: {
            type: Boolean,
            default: false,
            required: false,
        },
    },

    computed: {
        buttonClasses() {
            return {
                [`postnl-button--${this.size}`]: this.size,
                'postnl-button--block': this.block,
                'postnl-button--disabled': this.disabled,
                'postnl-button--square': this.square,
                'postnl-button--ghost': this.ghost,
            };
        },

        contentVisibilityClass() {
            return {
                'is--hidden': this.isLoading,
            };
        },

        filteredAttributes() {
            const attributes = { ...this.$attrs };

            if (this.disabled) {
                // Remove onClick event if button is disabled
                attributes.onClick = null;
            }

            return attributes;
        },
    },
};
