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

class Migration1708424816CreateV3_1_0IdCheckProducts extends ProductMigration
{
    public function getCreationTimestamp(): int
    {
        return 1708424816;
    }

    public function update(Connection $connection): void
    {
        $products = [
        [
            'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPMENT_NL_NL_3438),
            'product_code_delivery' => '3438',
            'source_zone' => Zone::NL,
            'destination_zone' => Zone::NL,
            'delivery_type' => DeliveryType::SHIPMENT,
            ProductDefinition::STOR_HOME_ALONE => 0,
            ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
            ProductDefinition::STOR_INSURANCE => 0,
            ProductDefinition::STOR_SIGNATURE => 0,
            ProductDefinition::STOR_AGE_CHECK => 1,
            ProductDefinition::STOR_NOTIFICATION => null,
        ],
        [
            'id' => Uuid::fromHexToBytes(Defaults::PRODUCT_PICKUP_NL_NL_3571),
            'product_code_delivery' => '3571',
            'source_zone' => Zone::NL,
            'destination_zone' => Zone::NL,
            'delivery_type' => DeliveryType::PICKUP,
            ProductDefinition::STOR_HOME_ALONE => 0,
            ProductDefinition::STOR_RETURN_IF_NOT_HOME => 0,
            ProductDefinition::STOR_INSURANCE => 0,
            ProductDefinition::STOR_SIGNATURE => 0,
            ProductDefinition::STOR_AGE_CHECK => 1,
            ProductDefinition::STOR_NOTIFICATION => null,
        ],
    ];

        $this->insertProducts($connection, $products);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
