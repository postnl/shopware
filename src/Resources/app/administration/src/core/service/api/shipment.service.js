import PostNlApiService from "../postnl-api.service";

export default class ShipmentService extends PostNlApiService
{
    generateBarcodes(orderIds) {
        return this.get('shipment/barcodes', {
            orderIds: orderIds,
        })
    }

    createShipments(orderIds, overrideProduct, overrideProductId, confirmShipments, downloadLabels) {
        return this.getBlob('shipment/labels', {
            orderIds: orderIds,
            overrideProduct: overrideProduct,
            overrideProductId: overrideProductId,
            confirmShipments: confirmShipments,
            downloadLabels: downloadLabels,
        });
    }

}
