import PostNlApiService from "./postnl-api.service";

export default class ProductSelectionService extends PostNlApiService
{
    sourceZoneHasProducts(sourceZone) {
        return this.get('product/source', {
            sourceZone: sourceZone
        });
    }

    getAvailableDeliveryTypes(sourceZone, destinationZone) {
        return this.get('product/delivery-types', {
            sourceZone: sourceZone,
            destinationZone: destinationZone
        })
    }

    select(sourceZone, destinationZone, deliveryType, options) {
        return this.get('product/select', {
            sourceZone: sourceZone,
            destinationZone: destinationZone,
            deliveryType: deliveryType,
            options: options,
        })
    }

    options(sourceZone, destinationZone, deliveryType) {
        return this.get('product/options', {
            sourceZone: sourceZone,
            destinationZone: destinationZone,
            deliveryType: deliveryType
        })
    }
}
