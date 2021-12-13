import template from './memo-config.html.twig'

const { Component, Mixin } = Shopware;

Component.register('memo-config', {
    template,

    mixins: [
        Mixin.getByName('sw-form-field'),
    ],

    props: {
        // eslint-disable-next-line vue/require-prop-types
        value: {
            required: false,
        },
        disabled: {
            type: Boolean,
            required: false,
            default: false,
        },
        required: {
            type: Boolean,
            required: false,
            default: false,
        }
    },

    data() {
        return {
            content: {},
        };
    },

    watch: {
        value: {
            handler(value) {
                this.content = JSON.parse(value || '{}');
            },
            immediate: true,
        },
        content: {
            handler(value) {
                this.$emit('input', JSON.stringify(value));
            },
            deep: true
        }
    },
});
