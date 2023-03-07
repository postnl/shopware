<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MigrationExecuteTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1674128763RenameOptionTables extends MigrationStep
{
    use MigrationExecuteTrait;

    public function getCreationTimestamp(): int
    {
        return 1674128763;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
    RENAME TABLE `postnl_product_option` TO `postnl_option`;
    RENAME TABLE `postnl_product_option_translation` TO `postnl_option_translation`;
    RENAME TABLE `postnl_product_option_mapping` TO `postnl_product_option_required_mapping`;

    ALTER TABLE `postnl_option_translation` CHANGE `postnl_product_option_id` `postnl_option_id` BINARY(16) NOT NULL;
    ALTER TABLE `postnl_option_translation` RENAME INDEX `fk.postnl_product_option_translation.product_option_id` TO `fk.postnl_option_translation.postnl_option_id`;
    ALTER TABLE `postnl_option_translation` RENAME INDEX `fk.postnl_product_option_translation.language_id` TO `fk.postnl_option_translation.language_id`;

    ALTER TABLE `postnl_product_option_required_mapping` CHANGE `product_option_id` `option_id` BINARY(16) NOT NULL;
    ALTER TABLE `postnl_product_option_required_mapping` RENAME INDEX `fk.postnl_product_option_mapping.product_id` TO `fk.postnl_product_option_required_mapping.product_id`;
    ALTER TABLE `postnl_product_option_required_mapping` RENAME INDEX `fk.postnl_product_option_mapping.product_option_id` TO `fk.postnl_product_option_required_mapping.option_id`;
SQL;

        $this->execute($connection, $sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
