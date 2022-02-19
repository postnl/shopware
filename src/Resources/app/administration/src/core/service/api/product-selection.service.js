import PostNlApiService from "../postnl-api.service";

export default class ProductSelectionService extends PostNlApiService
{
    getProduct(productId) {
        return this.get('product', {
            productId: productId
        })
    }

    getDefaultProduct(sourceZone, destinationZone, deliveryType) {
        return this.get('product/default', {
            sourceZone: sourceZone,
            destinationZone: destinationZone,
            deliveryType: deliveryType
        });
    }

    selectProduct(sourceZone, destinationZone, deliveryType, flags, changedFlags) {
        return this.get('product/select', {
            sourceZone: sourceZone,
            destinationZone: destinationZone,
            deliveryType: deliveryType,
            flags: flags,
            changedFlags: changedFlags,
        });
    }

    sourceZoneHasProducts(sourceZone) {
        return this.get('product/source-zone', {
            sourceZone: sourceZone
        });
    }

    getDeliveryTypes(sourceZone, destinationZone) {
        return this.get('product/delivery-types', {
            sourceZone: sourceZone,
            destinationZone: destinationZone
        });
    }

    getFlagsForProduct(productId) {
        return this.get('product/flags', {
            productId: productId
        });
    }

    getAvailableFlags(sourceZone, destinationZone, deliveryType) {
        return this.get('product/flags/available', {
            sourceZone: sourceZone,
            destinationZone: destinationZone,
            deliveryType: deliveryType
        });
    }

}
