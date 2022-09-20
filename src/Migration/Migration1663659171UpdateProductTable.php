<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MigrationExecuteTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1663659171UpdateProductTable extends MigrationStep
{
    use MigrationExecuteTrait;

    public function getCreationTimestamp(): int
    {
        return 1663659171;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `postnl_product` DROP INDEX `un.postnl_product.product_code_delivery`;
ALTER TABLE `postnl_product` ADD UNIQUE `un.postnl_product.target_product` (`product_code_delivery`, `source_zone`, `destination_zone`);
SQL;

        $this->execute($connection, $sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
