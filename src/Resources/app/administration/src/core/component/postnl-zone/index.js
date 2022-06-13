import template from './postnl-zone.html.twig';

Shopware.Component.extend('postnl-zone', 'sw-condition-base', {
    template,

    computed: {
        operators() {
            return this.conditionDataProviderService.addEmptyOperatorToOperatorSet(
                this.conditionDataProviderService.getOperatorSet('multiStore'),
            );
        },
        zones: {
            get() {
                return [
                    {value:'NL', label:this.$tc('postnl.rules.shipping.zone.nl')},
                    {value:'BE', label:this.$tc('postnl.rules.shipping.zone.be')},
                    {value:'GLOBAL', label:this.$tc('postnl.rules.shipping.zone.global')},
                    {value:'EU', label:this.$tc('postnl.rules.shipping.zone.eu')},
                ];
            }
        },
        postNLZones: {
            get() {
                this.ensureValueExist();
                return this.condition.value.postNLZones || [];
            },
            set(postNLZones) {
                this.ensureValueExist();
                this.condition.value = {...this.condition.value, postNLZones};
            },
        }
    },
    methods:{
        setPostNLZones(zones) {
            this.postNLZones = zones;
        },
    }
});
