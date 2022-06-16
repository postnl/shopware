import template from './postnl-config-description-text.html.twig';
import './postnl-config-description-text.scss';


// eslint-disable-next-line no-undef
const {Component, Mixin} = Shopware;
Component.register('postnl-config-description-text', {
    template,

    mixins: [
        Mixin.getByName('notification'),
    ],

    props: {
        label: {
            type: String,
            required: false,
            default: '',
        },
    }

});
