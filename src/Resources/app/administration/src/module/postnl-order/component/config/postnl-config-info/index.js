import template from './postnl-config-info.html.twig';
import './postnl-config-info.scss';

// eslint-disable-next-line no-undef
const { Mixin, Store} = Shopware;

export default {
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
            const user = Store.get('session').currentUser;

            if (!user) {
                return '';
            }

            if (user.firstName === '') {
                return user.username;
            }

            return user.firstName;
        },
    },
}
