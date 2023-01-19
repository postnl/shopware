<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\OptionMigration;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1674138063CreateV2_0Options extends OptionMigration
{
    public function getCreationTimestamp(): int
    {
        return 1674138063;
    }

    public function update(Connection $connection): void
    {
        $options = [

        ];
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
