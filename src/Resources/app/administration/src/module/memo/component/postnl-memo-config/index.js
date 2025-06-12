import template from './postnl-memo-config.html.twig';

const { Mixin } = Shopware;
const { string } = Shopware.Utils;

export default {
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
            fieldType: 'block',
            content: {},
        };
    },

    computed: {
        field() {
            return `sw-${this.fieldType}-field`;
        }
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
                this.updateValue(value);
            },
            deep: true
        }
    },

    methods: {
        updateValue(value) {
            const cleanedValue = Object.fromEntries(Object.entries(value).filter(([k, v]) => {
                return typeof v === 'string'
                    ? !string.isEmptyOrSpaces(v)
                    : true;
            }));
            this.$emit('update:value', JSON.stringify(cleanedValue));
        }
    }
}
