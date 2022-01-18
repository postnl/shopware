import template from './memo-config.html.twig';

const { Component, Mixin } = Shopware;
const { string } = Shopware.Utils;

Component.register('memo-config', {
    template,

    mixins: [
        Mixin.getByName('sw-form-field'),
    ],

    props: {
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
                const cleanedValue = Object.fromEntries(Object.entries(value).filter(([k, v]) => {
                    return typeof v === 'string'
                        ? !string.isEmptyOrSpaces(v)
                        : true;
                }));
                this.$emit('input', JSON.stringify(cleanedValue));
            },
            deep: true
        }
    },
});
