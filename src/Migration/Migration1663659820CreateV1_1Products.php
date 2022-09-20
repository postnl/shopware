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

class Migration1663659820CreateV1_1Products extends ProductMigration
{
    public function getCreationTimestamp(): int
    {
        return 1663659820;
    }

    public function update(Connection $connection): void
    {
        $products = [
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_BE_BE_4960),
                'product_code_delivery' => '4960',
                'source_zone' => Zone::BE,
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
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_BE_BE_4961),
                'product_code_delivery' => '4961',
                'source_zone' => Zone::BE,
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
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_BE_BE_4962),
                'product_code_delivery' => '4962',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::BE,
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
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_BE_BE_4963),
                'product_code_delivery' => '4963',
                'source_zone' => Zone::BE,
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
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_BE_BE_4965),
                'product_code_delivery' => '4965',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::BE,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 1,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 1,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
                'created_at' => (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT)
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_PICKUP_BE_BE_4878),
                'product_code_delivery' => '4878',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::BE,
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
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_PICKUP_BE_BE_4880),
                'product_code_delivery' => '4880',
                'source_zone' => Zone::BE,
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
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_BE_EU_4952),
                'product_code_delivery' => '4952',
                'source_zone' => Zone::BE,
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
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_BE_GLOBAL_4947),
                'product_code_delivery' => '4947',
                'source_zone' => Zone::BE,
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
        // implement update destructive
    }
}
