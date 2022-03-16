import PostNlApiService from "../postnl-api.service";

export default class ProductSelectionService extends PostNlApiService
{
    constructor(httpClient, loginService, apiBasePath = 'product') {
        super(httpClient, loginService, apiBasePath);
    }

    getProduct(productId) {
        return this.get('', {
            productId: productId,
        })
    }

    getDefaultProduct(sourceZone, destinationZone, deliveryType) {
        return this.get('default', {
            sourceZone: sourceZone,
            destinationZone: destinationZone,
            deliveryType: deliveryType,
        });
    }

    selectProduct(sourceZone, destinationZone, deliveryType, flags, changedFlags) {
        return this.get('select', {
            sourceZone: sourceZone,
            destinationZone: destinationZone,
            deliveryType: deliveryType,
            flags: flags,
            changedFlags: changedFlags,
        });
    }

    sourceZoneHasProducts(sourceZone) {
        return this.get('source-zone', {
            sourceZone: sourceZone,
        });
    }

    getDeliveryTypes(sourceZone, destinationZone) {
        return this.get('delivery-types', {
            sourceZone: sourceZone,
            destinationZone: destinationZone,
        });
    }

    getFlagsForProduct(productId) {
        return this.get('flags', {
            productId: productId,
        });
    }

    getAvailableFlags(sourceZone, destinationZone, deliveryType) {
        return this.get('flags/available', {
            sourceZone: sourceZone,
            destinationZone: destinationZone,
            deliveryType: deliveryType,
        });
    }

}
