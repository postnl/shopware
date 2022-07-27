const { Context, Mixin } = Shopware;

Mixin.register('postnl-config-sender-country', {

    mixins: [
        Mixin.getByName('postnl-memo-config-access'),
    ],

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            senderCountry: null,
        }
    },

    computed: {
        countryRepository() {
            return this.repositoryFactory.create('country');
        },

        senderCountryId() {
            return this.getJsonConfigItem('senderAddress')?.country;
        }
    },

    watch: {
        senderCountryId: {
            handler(value) {
                if(!!value) {
                    this.getSenderCountry(value);
                    return;
                }

                this.country = null;
            },
            immediate: true,
        }
    },

    methods: {
        getSenderCountry(countryId) {
            this.countryRepository
                .get(countryId, Context.api)
                .then(country => {
                    this.senderCountry = country;
                });
        }
    }
});
