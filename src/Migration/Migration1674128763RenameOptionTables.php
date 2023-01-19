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
SQL;

        $this->execute($connection, $sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
