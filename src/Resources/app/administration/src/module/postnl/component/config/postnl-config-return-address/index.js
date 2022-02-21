import template from './postnl-config-return-address.html.twig';

const { Component, Mixin } = Shopware;

Component.extend('postnl-config-return-address', 'memo-config', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('memo-config-access'),
        Mixin.getByName('memo-grid-span'),
    ],

    data() {
        return {
            country: null,
        }
    },

    computed: {
        countryRepository() {
            return this.repositoryFactory.create('country');
        },

        countryId() {
            return this.getJsonConfigItem('senderAddress')?.country;
        }
    },

    watch: {
        countryId: {
            handler(value) {
                if(!!value) {
                    this.getCountry(value);
                    return;
                }

                this.country = null;
            },
            immediate: true,
        }
    },

    methods: {
        getCountry(countryId) {
            this.countryRepository
                .get(countryId, Shopware.Context.api)
                .then(country => {
                    this.country = country;
                });
        }
    }
})
