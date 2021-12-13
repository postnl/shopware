import template from "./postnl-api-credentials.html.twig";

const {Component, Mixin} = Shopware;

Component.register('postnl-api-credentials', {
    template,

    inject: [
        'PostNlApiCredentialsService',
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            isLoading: false,
        }
    },

    methods: {
        onTestButtonClicked() {
            this.isLoading = true;

            const apiKeyInput = document.querySelector('input[name="PostNlShipments.config.apiKey"]');

            const apiKey = !!apiKeyInput ? apiKeyInput.value : null;
            //
            this.PostNlApiCredentialsService.checkCredentials(apiKey)
                .then((response) => {
                    if (response.valid === true) {
                        this.createNotificationSuccess({
                            title: this.$tc('global.default.success'),
                            message: this.$tc(response.message),
                        });
                    } else {
                        this.createNotificationError({
                            title: this.$tc('global.default.error'),
                            message: this.$tc(response.message),
                        });
                    }
                })
                .catch((response) => {
                    console.error(response);
                    this.createNotificationError({
                        title: this.$tc('global.default.error'),
                        message: this.$tc('global.notification.unspecifiedSaveErrorMessage'),
                    });
                })
                .finally(() => this.isLoading = false);
        }
    }
});
