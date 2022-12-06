<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MigrationExecuteTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1666258700RemoveProductOptionMappingCreatedAt extends MigrationStep
{
    use MigrationExecuteTrait;
    
    public function getCreationTimestamp(): int
    {
        return 1666258700;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `postnl_product_option_mapping` DROP `created_at`;
SQL;

        $this->execute($connection, $sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
