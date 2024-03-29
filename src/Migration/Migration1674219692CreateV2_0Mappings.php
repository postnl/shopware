<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MappingMigration;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Entity\Product\Aggregate\ProductOptionOptionalMappingDefinition;

class Migration1674219692CreateV2_0Mappings extends MappingMigration
{
    public function getCreationTimestamp(): int
    {
        return 1674219692;
    }

    public function update(Connection $connection): void
    {
        $this->insert($connection, ProductOptionOptionalMappingDefinition::ENTITY_NAME, [
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3085,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3087,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3089,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3090,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3094,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3096,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3189,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3385,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3389,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_NL_3390,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_BE_4912,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_BE_4914,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_BE_4941,
                'option_id'  => Defaults::OPTION_118_006,
            ],
            [
                'product_id' => Defaults::PRODUCT_SHIPMENT_NL_BE_4946,
                'option_id'  => Defaults::OPTION_118_006,
            ],
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
