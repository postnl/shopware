import template from "./postnl-config-api-credentials-test.html.twig";
import './postnl-config-api-credentials-test.scss';

const { Mixin } = Shopware;

export default {
    template,

    inject: [
        'PostNlApiCredentialsService',
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('postnl-memo-config-access'),
    ],

    data() {
        return {
            isLoading: false,
        }
    },

    methods: {
        onTestButtonClicked() {
            this.isLoading = true;

            const productionApiKey = this.getConfigItem('productionApiKey');
            const sandboxApiKey = this.getConfigItem('sandboxApiKey');

            Promise
                .all([
                    this.createApiKeyTestPromise(productionApiKey, false),
                    this.createApiKeyTestPromise(sandboxApiKey, true),
                ])
                .finally(() => this.isLoading = false)
        },

        createApiKeyTestPromise(apikey, sandbox) {
            if(!apikey) {
                return Promise.resolve();
            }

            return this.PostNlApiCredentialsService.checkCredentials(apikey, sandbox)
                .then((response) => {
                    const keySnippet = sandbox === true
                        ? this.$t('postnl.config.api.sandboxApiKey')
                        : this.$t('postnl.config.api.productionApiKey');

                    if (response.valid === true) {
                        this.createNotificationSuccess({
                            title: this.$t('global.default.success'),
                            message: `${keySnippet} ${this.$t('postnl.config.api.isValid')}`,
                        });
                    } else {
                        this.createNotificationError({
                            title: this.$t('global.default.error'),
                            message: `${keySnippet} ${this.$t('postnl.config.api.isInvalid')}`,
                        });
                    }
                })
                .catch(() => {
                    this.createNotificationError({
                        title: this.$t('global.default.error'),
                        message: this.$t('global.notification.unspecifiedSaveErrorMessage'),
                    });
                });
        }
    }
}