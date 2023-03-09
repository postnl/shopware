<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MigrationExecuteTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1678290763UpdateTablesV2_0 extends MigrationStep
{
    use MigrationExecuteTrait;

    public function getCreationTimestamp(): int
    {
        return 1678290763;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `postnl_product` ADD `replaced_by_id` BINARY(16) NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `postnl_product` ADD KEY `fk.postnl_product.replaced_by_id` (`replaced_by_id`);
ALTER TABLE `postnl_product` ADD CONSTRAINT `fk.postnl_product.replaced_by_id` FOREIGN KEY (`replaced_by_id`)
    REFERENCES `postnl_product`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `postnl_option` ADD `hidden` BOOLEAN NOT NULL DEFAULT FALSE AFTER `option`;
UPDATE `postnl_option` SET `hidden` = '1';
SQL;

        $this->execute($connection, $sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
