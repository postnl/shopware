import template from './postnl-config-customer-data.html.twig';

const { Mixin } = Shopware;

export default {
    template,

    mixins: [
        Mixin.getByName('postnl-memo-grid-span')
    ]
}
