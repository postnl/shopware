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

    protected function createQuotedFields(
        Connection $connection,
        array $fields = [],
        string $alias = ''
    ): array
    {
        return array_map(function($field) use ($connection, $alias) {
            if(!empty($alias)) {
                return $connection->quoteIdentifier($alias) . '.' . $connection->quoteIdentifier($field);
            }
            return $connection->quoteIdentifier($field);
        }, $fields);
    }

    protected function createQuotedJoinParts(
        Connection $connection,
        string $leftAlias,
        string $rightTable,
        string $rightAlias,
        string $leftOnField,
        string $rightOnField
    ): array {
        return [
            $connection->quoteIdentifier($leftAlias),
            $connection->quoteIdentifier($rightTable),
            $connection->quoteIdentifier($rightAlias),
            sprintf(
                '%s.%s = %s.%s',
                $connection->quoteIdentifier($leftAlias),
                $connection->quoteIdentifier($leftOnField),
                $connection->quoteIdentifier($rightAlias),
                $connection->quoteIdentifier($rightOnField),
            )
        ];
    }
}
