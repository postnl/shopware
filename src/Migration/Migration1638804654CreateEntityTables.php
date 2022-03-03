<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MigrationExecuteTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1638804654CreateEntityTables extends MigrationStep
{
    use MigrationExecuteTrait;

    public function getCreationTimestamp(): int
    {
        return 1638804654;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `postnl_shipments_product` (
    `id` BINARY(16) NOT NULL,
    `product_code_delivery` VARCHAR(255) NOT NULL,
    `source_zone` VARCHAR(255) NOT NULL,
    `destination_zone` VARCHAR(255) NOT NULL,
    `delivery_type` VARCHAR(255) NOT NULL,
    `home_alone` TINYINT(1) NULL DEFAULT '0',
    `return_if_not_home` TINYINT(1) NULL DEFAULT '0',
    `insurance` TINYINT(1) NULL DEFAULT '0',
    `signature` TINYINT(1) NULL DEFAULT '0',
    `age_check` TINYINT(1) NULL DEFAULT '0',
    `notification` TINYINT(1) NULL DEFAULT '0',
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `un.postnl_shipments_product.product_code_delivery` UNIQUE (`product_code_delivery`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `postnl_shipments_product_translation` (
    `name` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `postnl_shipments_product_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`postnl_shipments_product_id`,`language_id`),
    KEY `fk.postnl_shipments_product_translation.product_id` (`postnl_shipments_product_id`),
    KEY `fk.postnl_shipments_product_translation.language_id` (`language_id`),
    CONSTRAINT `fk.postnl_shipments_product_translation.product_id` FOREIGN KEY (`postnl_shipments_product_id`)
        REFERENCES `postnl_shipments_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.postnl_shipments_product_translation.language_id` FOREIGN KEY (`language_id`)
        REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `postnl_shipments_product_option` (
    `id` BINARY(16) NOT NULL,
    `characteristic` VARCHAR(3) NOT NULL,
    `option` VARCHAR(3) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `un.postnl_shipments_product_option.product_option` UNIQUE (`characteristic`, `option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `postnl_shipments_product_option_translation` (
    `name` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `postnl_shipments_product_option_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`postnl_shipments_product_option_id`,`language_id`),
    KEY `fk.postnl_shipments_product_option_translation.product_option_id` (`postnl_shipments_product_option_id`),
    KEY `fk.postnl_shipments_product_option_translation.language_id` (`language_id`),
    CONSTRAINT `fk.postnl_shipments_product_option_translation.product_option_id` FOREIGN KEY (`postnl_shipments_product_option_id`)
        REFERENCES `postnl_shipments_product_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.postnl_shipments_product_option_translation.language_id` FOREIGN KEY (`language_id`)
        REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `postnl_shipments_product_option_mapping` (
    `product_id` BINARY(16) NOT NULL,
    `product_option_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    PRIMARY KEY (`product_option_id`,`product_id`),
    KEY `fk.postnl_shipments_product_option_mapping.product_id` (`product_id`),
    KEY `fk.postnl_shipments_product_option_mapping.product_option_id` (`product_option_id`),
    CONSTRAINT `fk.postnl_shipments_product_option_mapping.product_id` FOREIGN KEY (`product_id`)
        REFERENCES `postnl_shipments_product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.postnl_shipments_product_option_mapping.product_option_id` FOREIGN KEY (`product_option_id`)
        REFERENCES `postnl_shipments_product_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $this->execute($connection, $sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS
    `postnl_shipments_product_option_mapping`,
    `postnl_shipments_product_option_translation`,
    `postnl_shipments_product_option`,
    `postnl_shipments_product_translation`,
    `postnl_shipments_product`;
SQL;

        $this->execute($connection, $sql);
    }
}
