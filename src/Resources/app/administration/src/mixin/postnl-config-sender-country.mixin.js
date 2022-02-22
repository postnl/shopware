const { Context, Mixin } = Shopware;

Mixin.register('postnl-config-sender-country', {

    inject: [
        'repositoryFactory'
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
                .get(countryId, Context.api)
                .then(country => {
                    this.country = country;
                });
        }
    }
});
