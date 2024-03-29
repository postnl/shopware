<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MappingMigration;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Entity\Product\Aggregate\ProductOptionOptionalMappingDefinition;
use PostNL\Shopware6\Entity\Product\Aggregate\ProductOptionRequiredMappingDefinition;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1678368385CreateV2_0InternationalMappings extends MappingMigration
{
    public function getCreationTimestamp(): int
    {
        return 1678368385;
    }

    public function update(Connection $connection): void
    {
        $this->insert($connection, ProductOptionRequiredMappingDefinition::ENTITY_NAME, [
            // EU Required
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_EU_4907_005_025,
                'option_id'  => Defaults::OPTION_101_012,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_EU_4907_004_015,
                'option_id'  => Defaults::OPTION_101_012,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_EU_4907_004_016,
                'option_id'  => Defaults::OPTION_101_012,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_BE_EU_4907_005_025,
                'option_id'  => Defaults::OPTION_101_012,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_BE_EU_4907_004_015,
                'option_id'  => Defaults::OPTION_101_012,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_BE_EU_4907_004_016,
                'option_id'  => Defaults::OPTION_101_012,
            ],
            // Uninsured
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_EU_4907_005_025,
                'option_id'  => Defaults::OPTION_005_025,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_BE_EU_4907_005_025,
                'option_id'  => Defaults::OPTION_005_025,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_GLOBAL_4909_005_025,
                'option_id'  => Defaults::OPTION_005_025,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_BE_GLOBAL_4909_005_025,
                'option_id'  => Defaults::OPTION_005_025,
            ],
            // Insurance
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_EU_4907_004_015,
                'option_id'  => Defaults::OPTION_004_015,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_BE_EU_4907_004_015,
                'option_id'  => Defaults::OPTION_004_015,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_GLOBAL_4909_004_015,
                'option_id'  => Defaults::OPTION_004_015,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_BE_GLOBAL_4909_004_015,
                'option_id'  => Defaults::OPTION_004_015,
            ],
            // Insurance Plus
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_EU_4907_004_016,
                'option_id'  => Defaults::OPTION_004_016,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_BE_EU_4907_004_016,
                'option_id'  => Defaults::OPTION_004_016,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_GLOBAL_4909_004_016,
                'option_id'  => Defaults::OPTION_004_016,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_BE_GLOBAL_4909_004_016,
                'option_id'  => Defaults::OPTION_004_016,
            ],
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
