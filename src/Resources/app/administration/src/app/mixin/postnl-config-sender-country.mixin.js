const { Context, Mixin, Store } = Shopware;

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
        senderCountryId() {
            return this.getJsonConfigItem('senderAddress')?.country;
        }
    },

    watch: {
        senderCountryId: {
            handler(value) {
                if(!!value) {
                    this.senderCountry = Store.get('postnlCountryCache').getCountryByIso(value)
                    return
                }

                this.senderCountry = null
            },
            immediate: true,
        }
    },
});
