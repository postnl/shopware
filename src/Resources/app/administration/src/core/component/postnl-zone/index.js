import template from './postnl-zone.html.twig';

export default {
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
                    { value: 'NL', label: this.$t('postnl.rules.shipping.zone.nl') },
                    { value: 'BE', label: this.$t('postnl.rules.shipping.zone.be') },
                    { value: 'EU', label: this.$t('postnl.rules.shipping.zone.eu') },
                    { value: 'GLOBAL', label: this.$t('postnl.rules.shipping.zone.global') },
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
                this.condition.value = { ...this.condition.value, postNLZones };
            },
        }
    },
};
