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
        $productIds = [
            Defaults::PRODUCT_SHIPMENT_NL_NL_3085,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3087,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3089,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3090,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3094,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3096,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3189,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3385,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3389,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3390,
            Defaults::PRODUCT_SHIPMENT_NL_BE_4912,
            Defaults::PRODUCT_SHIPMENT_NL_BE_4914,
            Defaults::PRODUCT_SHIPMENT_NL_BE_4941,
            Defaults::PRODUCT_SHIPMENT_NL_BE_4946,
        ];

        $optionsIds = [
            Defaults::OPTION_118_006
        ];

        $data = $this->createMatrix('product_id', $productIds, 'option_id', $optionsIds);

        $this->insert($connection, ProductOptionOptionalMappingDefinition::ENTITY_NAME, $data);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
