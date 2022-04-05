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

    createShipments(orderIds, confirmShipments, downloadLabels) {
        return this.getBlob('create', {
            orderIds: orderIds,
            confirmShipments: confirmShipments,
            downloadLabels: downloadLabels,
        });
    }

    determineDestinationZones(orderIds) {
        return this.get('zones', {
            orderIds: orderIds,
        })
    }
}
