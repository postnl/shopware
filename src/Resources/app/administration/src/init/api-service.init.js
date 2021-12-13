import ApiCredentialsService from '../core/service/api-credentials.service';

// eslint-disable-next-line no-undef
const { Application } = Shopware;

Application.addServiceProvider('PostNlApiCredentialsService', (container) => {
    const initContainer = Application.getContainer('init');

    return new ApiCredentialsService(initContainer.httpClient, container.loginService);
});
