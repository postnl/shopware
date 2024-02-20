<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\ProductMigration;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Entity\Product\ProductDefinition;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1708424779CreateV3_1_0BelgianProducts extends ProductMigration
{
    public function getCreationTimestamp(): int
    {
        return 1708424779;
    }

    public function update(Connection $connection): void
    {
        $products = [
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPMENT_BE_NL_4890),
                'product_code_delivery' => '4890',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPMENT_BE_NL_4891),
                'product_code_delivery' => '4891',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPMENT_BE_NL_4893),
                'product_code_delivery' => '4893',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 1,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 0,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPMENT_BE_NL_4894),
                'product_code_delivery' => '4894',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 1,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPMENT_BE_NL_4896),
                'product_code_delivery' => '4896',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 1,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPMENT_BE_NL_4897),
                'product_code_delivery' => '4897',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE => 0,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
                ProductDefinition::STOR_INSURANCE => 1,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => null,
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_PICKUP_BE_NL_4898),
                'product_code_delivery' => '4898',
                'source_zone' => Zone::BE,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::PICKUP,
                ProductDefinition::STOR_HOME_ALONE => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => null,
                ProductDefinition::STOR_INSURANCE => 0,
                ProductDefinition::STOR_SIGNATURE => 1,
                ProductDefinition::STOR_AGE_CHECK => 0,
                ProductDefinition::STOR_NOTIFICATION => 0,
            ],
        ];

        $this->insertProducts($connection, $products);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
