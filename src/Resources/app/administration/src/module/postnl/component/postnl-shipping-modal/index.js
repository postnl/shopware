import template from './postnl-shipping-modal.html.twig';
// import './postnl-shipping-modal.scss';

// eslint-disable-next-line no-undef
const {Component, Mixin, } = Shopware;

Component.register('postnl-shipping-modal', {
    template,

    inject: [
    ],

    mixins: [
        Mixin.getByName('notification'),
    ],

    props: {
        selection: {
            type: Object,
            required: true,
        }
    },

    data() {
        return {
            isLoading: false,
        }
    },

    computed: {
    },

    mounted() {
    },

    methods: {
    },
});
