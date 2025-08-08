import template from './postnl-config-return-options.html.twig';

const { Mixin } = Shopware;

export default {
    template,

    mixins: [
        Mixin.getByName('postnl-memo-grid-span'),
    ],

    data() {
        return {
            fieldType: 'base',
        }
    },

    computed: {
        typeOptions() {
            return [
                'none', 'labelInTheBox'
            ]
                .map(value => ({
                    value,
                    label: this.$t(`postnl.config.returnOptions.options.${value}`),
                }))
        }
    },

    mounted() {
        this.mountedComponent();
    },

    methods: {
        mountedComponent() {
            this.setDefaults()
        },

        setDefaults() {
            const defaults = {
                type: 'none',
            }

            Object.entries(defaults)
                .forEach(([key, value]) => {
                    if (!this.content.hasOwnProperty(key)) {
                        this.content[key] = value;
                        this.updateValue(this.content);
                    }
                })
        }
    },
}
