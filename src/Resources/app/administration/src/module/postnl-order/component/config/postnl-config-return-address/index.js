import template from './postnl-config-return-address.html.twig';

const { Mixin } = Shopware;

export default {
    template,

    mixins: [
        Mixin.getByName('postnl-memo-grid-span'),
        Mixin.getByName('postnl-config-sender-country'),
    ],

    watch: {
        senderCountry(value) {
            this.content.countrycode = value.iso;
        }
    }
}
