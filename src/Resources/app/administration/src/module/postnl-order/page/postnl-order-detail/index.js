import template from './postnl-order-detail.html.twig';

// const { , State } = Shopware;
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
        ...mapState('swOrderDetail', [
            'order',
            'versionContext',
        ]),
    },

    created() {
        if(!this.versionContext) {
            // TODO replace with Store
            // State.commit('swOrderDetail/setVersionContext', Shopware.Context.api);
            this.reloadEntityData();
        }
    },
}
