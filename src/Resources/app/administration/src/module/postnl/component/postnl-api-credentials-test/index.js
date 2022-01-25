import template from "./postnl-api-credentials-test.html.twig";

const { Component, Mixin } = Shopware;

Component.register('postnl-api-credentials-test', {
    template,

    inject: [
        'PostNlApiCredentialsService',
    ],

    mixins: [
        Mixin.getByName('notification'),
        Mixin.getByName('memo-config-access'),
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
                        ? this.$tc('postnl.shipments.config.api.sandboxApiKey')
                        : this.$tc('postnl.shipments.config.api.productionApiKey');

                    if (response.valid === true) {
                        this.createNotificationSuccess({
                            title: this.$tc('global.default.success'),
                            message: `${keySnippet} ${this.$tc('postnl.shipments.config.api.isValid')}`,
                        });
                    } else {
                        this.createNotificationError({
                            title: this.$tc('global.default.error'),
                            message: `${keySnippet} ${this.$tc('postnl.shipments.config.api.isInvalid')}`,
                        });
                    }
                })
                .catch(() => {
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: this.$tc('global.notification.unspecifiedSaveErrorMessage'),
                    });
                });
        }
    }
});
