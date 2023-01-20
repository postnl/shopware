<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MigrationExecuteTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1674128784CreateOptionalOptionMappingTable extends MigrationStep
{
    use MigrationExecuteTrait;

    public function getCreationTimestamp(): int
    {
        return 1674128784;
    }

    public function update(Connection $connection): void
    {
       $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `postnl_product_option_optional_mapping` (
    `product_id` BINARY(16) NOT NULL,
    `option_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`product_id`,`option_id`),
    KEY `fk.postnl_product_option_optional_mapping.product_id` (`product_id`),
    KEY `fk.postnl_product_option_optional_mapping.option_id` (`option_id`),
    CONSTRAINT `fk.postnl_product_option_optional_mapping.product_id` FOREIGN KEY (`product_id`)
        REFERENCES `postnl_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.postnl_product_option_optional_mapping.option_id` FOREIGN KEY (`option_id`)
        REFERENCES `postnl_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $this->execute($connection, $sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
