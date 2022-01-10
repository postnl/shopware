<?php

namespace PostNL\Shipments\Component\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

trait MigrationExecuteTrait
{
    /**
     * @param Connection $connection
     * @param string $sql
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    protected function execute(Connection $connection, string $sql)
    {
        return (method_exists($connection, 'executeStatement'))
            ? $connection->executeStatement($sql)
            : $connection->executeUpdate($sql);
    }
}
