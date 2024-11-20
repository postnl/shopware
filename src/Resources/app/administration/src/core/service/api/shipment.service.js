import PostNlApiService from "../postnl-api.service";

export default class ShipmentService extends PostNlApiService
{
    constructor(httpClient, loginService, apiBasePath = 'shipment') {
        super(httpClient, loginService, apiBasePath);
    }

    generateBarcodes(orderIds) {
        return this.get('barcodes', {
            orderIds: orderIds,
        })
    }

    changeProducts(orderIds, productId) {
        return this.post('change', {
            orderIds: orderIds,
            productId: productId,
        })
    }

    /**
     * @param data {orderIds:array<string>, confirmShipments:bool, downloadLabels:bool, smartReturn:bool}
     * @returns {Promise<*|Promise<AxiosResponse<any>>>}
     */
    createShipments(data) {
        return this.getBlob('create', data);
    }

    createSmartReturn(data) {
        return this.get('create-smart-return', data)
    }

    activateReturnLabels(data) {
        return this.get('activate-return-label', data)
    }

    determineZones(orderIds) {
        return this.get('zones', {
            orderIds: orderIds,
        })
    }
}
