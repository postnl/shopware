<?php declare(strict_types=1);

namespace PostNL\Shopware6\Component\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

trait MigrationQuoteTrait
{
    /**
     * @param Connection $connection
     * @param array      $data
     * @return array
     */
    protected function quote(Connection $connection, array $data): array
    {
        return array_combine(
            array_map(
                function($key) use ($connection) {
                    return $connection->quoteIdentifier($key);
                },
                array_keys($data)
            ),
            array_values($data)
        );
    }
}
