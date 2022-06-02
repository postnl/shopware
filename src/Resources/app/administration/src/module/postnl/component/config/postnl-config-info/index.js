import template from './postnl-config-info.html.twig';
import './postnl-config-info.scss';


// eslint-disable-next-line no-undef
const {Component, Mixin} = Shopware;

Component.register('postnl-config-info', {
    template,

    mixins: [
        Mixin.getByName('notification'),
    ],


    computed: {
        /**
         *
         * @returns {string|*}
         */
        userName() {
            // eslint-disable-next-line no-undef
            const user = Shopware.State.get('session').currentUser;

            if (!user) {
                return '';
            }

            if (user.firstName === '') {
                return user.username;
            }

            return user.firstName;
        },
    },
});
