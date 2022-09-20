<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\ProductMigration;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Entity\Product\ProductDefinition;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use Shopware\Core\Defaults as ShopwareDefaults;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1640162702CreateV1Products extends ProductMigration
{
    public function getCreationTimestamp(): int
    {
        return 1640162702;
    }

    public function update(Connection $connection): void
    {
        $products = [
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3085),
                'product_code_delivery' => '3085',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3385),
                'product_code_delivery' => '3385',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 1,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3090),
                'product_code_delivery' => '3090',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 1,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3390),
                'product_code_delivery' => '3390',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 1,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 1,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3087),
                'product_code_delivery' => '3087',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 1,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3094),
                'product_code_delivery' => '3094',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 1,
                ProductDefinition::STOR_INSURANCE => 1,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3089),
                'product_code_delivery' => '3089',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 1,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3096),
                'product_code_delivery' => '3096',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 1,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 1,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3189),
                'product_code_delivery' => '3189',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_NL_3389),
                'product_code_delivery' => '3389',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 1,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_PICKUP_NL_NL_3533),
                'product_code_delivery' => '3533',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::PICKUP,
                ProductDefinition::STOR_HOME_ALONE => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => null,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => 0,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_PICKUP_NL_NL_3534),
                'product_code_delivery' => '3534',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::PICKUP,
                ProductDefinition::STOR_HOME_ALONE => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => null,
                ProductDefinition::STOR_INSURANCE => 1,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => 0,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_PICKUP_NL_NL_3543),
                'product_code_delivery' => '3543',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::PICKUP,
                ProductDefinition::STOR_HOME_ALONE => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => null,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => 1,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_PICKUP_NL_NL_3544),
                'product_code_delivery' => '3544',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::PICKUP,
                ProductDefinition::STOR_HOME_ALONE => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => null,
                ProductDefinition::STOR_INSURANCE => 1,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => 1,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [   // V2?
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_MAILBOX_NL_NL_2928),
                'product_code_delivery' => '2928',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::MAILBOX,
                ProductDefinition::STOR_HOME_ALONE => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => null,
                ProductDefinition::STOR_INSURANCE => null,
                ProductDefinition::STOR_SIGNATURE => null,
                ProductDefinition::STOR_AGE_CHECK => null,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_PICKUP_NL_BE_4936),
                'product_code_delivery' => '4936',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::BE,
                'delivery_type' => DeliveryType::PICKUP,
                ProductDefinition::STOR_HOME_ALONE => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => null,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => 0,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_BE_4941),
                'product_code_delivery' => '4941',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::BE,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 1,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_BE_4946),
                'product_code_delivery' => '4946',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::BE,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_BE_4912),
                'product_code_delivery' => '4912',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::BE,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_BE_4914),
                'product_code_delivery' => '4914',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::BE,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 1,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_EU_4952),
                'product_code_delivery' => '4952',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::EU,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => null,
                ProductDefinition::STOR_INSURANCE => null,
                ProductDefinition::STOR_SIGNATURE => null,
                ProductDefinition::STOR_AGE_CHECK => null,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_GLOBAL_4945),
                'product_code_delivery' => '4945',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::GLOBAL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => null,
                ProductDefinition::STOR_INSURANCE => null,
                ProductDefinition::STOR_SIGNATURE => null,
                ProductDefinition::STOR_AGE_CHECK => null,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
        ];

        $this->insertProducts($connection, $products);
    }

    public function updateDestructive(Connection $connection): void
    {
        //TODO figure out better lifecycle
//        $deleteIds = [
//            Defaults::PRODUCT_SHIPPING_NL_NL_3085,
//            Defaults::PRODUCT_SHIPPING_NL_NL_3385,
//            Defaults::PRODUCT_SHIPPING_NL_NL_3090,
//            Defaults::PRODUCT_SHIPPING_NL_NL_3390,
//            Defaults::PRODUCT_SHIPPING_NL_NL_3087,
//            Defaults::PRODUCT_SHIPPING_NL_NL_3094,
//            Defaults::PRODUCT_SHIPPING_NL_NL_3089,
//            Defaults::PRODUCT_SHIPPING_NL_NL_3096,
//            Defaults::PRODUCT_SHIPPING_NL_NL_3189,
//            Defaults::PRODUCT_SHIPPING_NL_NL_3389,
//            Defaults::PRODUCT_PICKUP_NL_NL_3533,
//            Defaults::PRODUCT_PICKUP_NL_NL_3534,
//            Defaults::PRODUCT_PICKUP_NL_NL_3543,
//            Defaults::PRODUCT_PICKUP_NL_NL_3544,
//            Defaults::PRODUCT_MAILBOX_NL_NL_2928,
//            Defaults::PRODUCT_PICKUP_NL_BE_4936,
//            Defaults::PRODUCT_SHIPPING_NL_BE_4941,
//            Defaults::PRODUCT_SHIPPING_NL_BE_4946,
//            Defaults::PRODUCT_SHIPPING_NL_BE_4912,
//            Defaults::PRODUCT_SHIPPING_NL_BE_4914,
//            Defaults::PRODUCT_SHIPPING_EU_4952,
//            Defaults::PRODUCT_SHIPPING_GLOBAL_4947,
//        ];
//
//        $this->deleteProducts($connection, $deleteIds);
    }
}
