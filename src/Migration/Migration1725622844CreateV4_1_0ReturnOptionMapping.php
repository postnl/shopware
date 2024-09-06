<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MappingMigration;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Entity\Product\Aggregate\ProductOptionOptionalMappingDefinition;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1725622844CreateV4_1_0ReturnOptionMapping extends MappingMigration
{
    public function getCreationTimestamp(): int
    {
        return 1725622844;
    }

    public function update(Connection $connection): void
    {
        $productIds = [
            Defaults::PRODUCT_SHIPMENT_NL_NL_3085,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3385,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3090,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3390,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3087,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3094,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3089,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3096,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3189,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3389,
            Defaults::PRODUCT_SHIPMENT_NL_NL_3438,
        ];

        $optionsIds = [
            Defaults::OPTION_152_025,
            Defaults::OPTION_152_026,
            Defaults::OPTION_152_028,
            Defaults::OPTION_191_001,
            Defaults::OPTION_191_004,
        ];

        $data = $this->createMatrix('product_id', $productIds, 'option_id', $optionsIds);

        $this->insert($connection, ProductOptionOptionalMappingDefinition::ENTITY_NAME, $data);
    }
}
