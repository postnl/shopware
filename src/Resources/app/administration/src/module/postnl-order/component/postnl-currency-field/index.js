import template from './postnl-currency-field.html.twig'

export default {
    template,

    props: {
        value: {
            required: false,
        },
    },

    data() {
        return {
            currentValue: this.value,
        };
    },

    watch: {
        value(value) {
            this.currentValue = value;
        },
        currentValue(value) {
            this.$emit("update:value", value);
        }
    },
}
