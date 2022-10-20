<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MigrationExecuteTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1666258800CreateOptionRequirementMappingTable extends MigrationStep
{
    use MigrationExecuteTrait;

    public function getCreationTimestamp(): int
    {
        return 1666258800;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `postnl_product_option_requirement` (
    `product_option_id` BINARY(16) NOT NULL,
    `required_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`product_option_id`,`required_id`),
    KEY `fk.postnl_product_option_mapping.product_option_id` (`product_option_id`),
    KEY `fk.postnl_product_option_mapping.required_id` (`required_id`),
    CONSTRAINT `fk.postnl_product_option_mapping.product_option_id` FOREIGN KEY (`product_option_id`)
        REFERENCES `postnl_product_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.postnl_product_option_mapping.required_id` FOREIGN KEY (`required_id`)
        REFERENCES `postnl_product_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $this->execute($connection, $sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
