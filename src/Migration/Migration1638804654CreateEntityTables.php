<?php declare(strict_types=1);

namespace PostNL\Shipments\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shipments\Component\Migration\MigrationExecuteTrait;
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
CREATE TABLE IF NOT EXISTS `postnl_shipments_product_code_config` (
    `id` BINARY(16) NOT NULL,
    `product_code_delivery` VARCHAR(255) NOT NULL,
    `source_zone` VARCHAR(255) NOT NULL,
    `destination_zone` VARCHAR(255) NOT NULL,
    `delivery_type` VARCHAR(255) NOT NULL,
    `next_door_delivery` TINYINT(1) NULL DEFAULT '0',
    `return_if_not_home` TINYINT(1) NULL DEFAULT '0',
    `insurance` TINYINT(1) NULL DEFAULT '0',
    `signature` TINYINT(1) NULL DEFAULT '0',
    `age_check` TINYINT(1) NULL DEFAULT '0',
    `notification` TINYINT(1) NULL DEFAULT '0',
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `un.postnl_shipments_pcc.product_code_delivery` UNIQUE (`product_code_delivery`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `postnl_shipments_product_code_config_translation` (
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `postnl_shipments_product_code_config_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`postnl_shipments_product_code_config_id`,`language_id`),
    KEY `fk.postnl_shipments_pcct.postnl_shipments_pcci` (`postnl_shipments_product_code_config_id`),
    KEY `fk.postnl_shipments_pcct.language_id` (`language_id`),
    CONSTRAINT `fk.postnl_shipments_pcct.postnl_shipments_pcci` FOREIGN KEY (`postnl_shipments_product_code_config_id`)
        REFERENCES `postnl_shipments_product_code_config` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.postnl_shipments_pcct.language_id` FOREIGN KEY (`language_id`)
        REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `postnl_shipments_product_option` (
    `id` BINARY(16) NOT NULL,
    `characteristic` VARCHAR(3) NOT NULL,
    `option` VARCHAR(3) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `un.postnl_shipments_pcc.product_option` UNIQUE (`characteristic`, `option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `postnl_shipments_product_option_translation` (
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    `postnl_shipments_product_option_id` BINARY(16) NOT NULL,
    `language_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`postnl_shipments_product_option_id`,`language_id`),
    KEY `fk.postnl_shipments_pot.postnl_shipments_poi` (`postnl_shipments_product_option_id`),
    KEY `fk.postnl_shipments_pot.language_id` (`language_id`),
    CONSTRAINT `fk.postnl_shipments_pot.postnl_shipments_poi` FOREIGN KEY (`postnl_shipments_product_option_id`)
        REFERENCES `postnl_shipments_product_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.postnl_shipments_pot.language_id` FOREIGN KEY (`language_id`)
        REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `postnl_shipments_product_code_option` (
    `product_option_id` BINARY(16) NOT NULL,
    `product_code_config_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    PRIMARY KEY (`product_option_id`,`product_code_config_id`),
    KEY `fk.postnl_shipments_pco.product_option_id` (`product_option_id`),
    KEY `fk.postnl_shipments_pco.product_code_config_id` (`product_code_config_id`),
    CONSTRAINT `fk.postnl_shipments_pco.product_option_id` FOREIGN KEY (`product_option_id`)
        REFERENCES `postnl_shipments_product_option` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.postnl_shipments_pco.product_code_config_id` FOREIGN KEY (`product_code_config_id`)
        REFERENCES `postnl_shipments_product_code_config` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $this->execute($connection, $sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS
    `postnl_shipments_product_code_option`,
    `postnl_shipments_product_option_translation`,
    `postnl_shipments_product_option`,
    `postnl_shipments_product_code_config_translation`,
    `postnl_shipments_product_code_config`;
SQL;

        $this->execute($connection, $sql);
    }
}
