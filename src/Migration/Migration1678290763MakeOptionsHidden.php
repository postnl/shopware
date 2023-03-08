<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MigrationExecuteTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1678290763MakeOptionsHidden extends MigrationStep
{
    use MigrationExecuteTrait;

    public function getCreationTimestamp(): int
    {
        return 1678290763;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `postnl_option` ADD `hidden` BOOLEAN NOT NULL DEFAULT FALSE AFTER `option`;
UPDATE `postnl_option` SET `hidden` = '1';
SQL;

    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
