import ApiCredentialsService from '../core/service/api/api-credentials.service';
import ProductSelectionService from '../core/service/api/product-selection.service';
import ShipmentService from '../core/service/api/shipment.service';

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

Application.addServiceProvider('ShipmentService', (container) => {
    const initContainer = Application.getContainer('init');

    return new ShipmentService(initContainer.httpClient, container.loginService);
});
