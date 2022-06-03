import template from './postnl-order-detail.html.twig';

const { Component, State } = Shopware;
const { mapState } = Shopware.Component.getComponentHelper();

Component.extend('postnl-order-detail', 'sw-order-detail', {
    template,

    data() {
        return {
            isChangeShippingModalOpen: false,
            isCreateShipmentModalOpen: false,
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
            State.commit('swOrderDetail/setVersionContext', Shopware.Context.api);
            this.reloadEntityData();
        }
    },

    methods: {
        onChangeShipping() {
            this.isChangeShippingModalOpen = false;
            location.reload();
        }
    }
})
