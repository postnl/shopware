import PostNlApiService from "../postnl-api.service";

export default class ShipmentService extends PostNlApiService
{
    generateBarcodes(orderIds) {
        return this.get('shipment/barcodes', {
            orderIds: orderIds,
        })
    }

    createShipments(orderIds, overrideProduct, overrideProductId) {
        return this.get('shipment/labels', {
            orderIds: orderIds,
            overrideProduct: overrideProduct,
            overrideProductId: overrideProductId,
        });
    }

}
