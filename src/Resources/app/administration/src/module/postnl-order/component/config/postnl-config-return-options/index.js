import template from './postnl-config-return-options.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-config-return-options', 'postnl-memo-config', {
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
                    label: this.$tc(`postnl.config.returnOptions.options.${value}`),
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
})
