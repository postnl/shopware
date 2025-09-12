import template from './postnl-order-detail.html.twig';

const { mapState } = Shopware.Component.getComponentHelper();

export default {
    template,

    data() {
        return {
            isChangeShippingModalOpen: false,
            isCreateShipmentModalOpen: false,
            isCreateReturnModalOpen: false,
        }
    },

    computed: {
        ...mapState(
            () => Shopware.Store.get('swOrderDetail'),
            [
                'order',
                'versionContext',
            ]
        ),
    },

    created() {
        if(!this.versionContext) {
            Shopware.Store.get('swOrderDetail').setVersionContext(Shopware.Context.api);
            this.reloadEntityData();
        }
    },
}
