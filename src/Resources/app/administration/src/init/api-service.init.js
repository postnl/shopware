import ApiCredentialsService from '../core/service/api-credentials.service';
import ProductSelectionService from '../core/service/product-selection.service';

// eslint-disable-next-line no-undef
const { Application } = Shopware;

Application.addServiceProvider('PostNlApiCredentialsService', (container) => {
    const initContainer = Application.getContainer('init');

    return new ApiCredentialsService(initContainer.httpClient, container.loginService);
});

Application.addServiceProvider('ProductSelectionService', (container) => {
    const initContainer = Application.getContainer('init');

    return new ProductSelectionService(initContainer.httpClient, container.loginService);
});
