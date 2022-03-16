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

    createShipments(orderIds, overrideProduct, overrideProductId, confirmShipments, downloadLabels) {
        return this.getBlob('labels', {
            orderIds: orderIds,
            overrideProduct: overrideProduct,
            overrideProductId: overrideProductId,
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
