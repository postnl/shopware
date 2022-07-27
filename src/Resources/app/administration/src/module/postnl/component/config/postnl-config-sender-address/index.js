import template from './postnl-config-sender-address.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.extend('postnl-config-sender-address', 'memo-config', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('postnl-memo-grid-span')
    ],

    data() {
        return {
            country: null,
        }
    },

    computed: {
        countryCriteria() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equalsAny('iso', ['NL'])); // Remove BE for now

            return criteria;
        },

        countryRepository() {
            return this.repositoryFactory.create('country');
        }
    },

    watch: {
        'content.country': {
            handler(value) {
                if(!!value) {
                    this.getCountryName(value);
                    return;
                }

                this.country = null;
            },
            immediate: true
        }
    },

    methods: {
        getCountryName(countryId) {
            this.countryRepository
                .get(countryId, Shopware.Context.api)
                .then(country => {
                    this.country = country.name;
                });
        }
    }
})
