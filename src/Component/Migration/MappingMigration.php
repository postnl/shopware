<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Component\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

abstract class MappingMigration extends MigrationStep
{
    /**
     * @param Connection $connection
     * @param string     $table
     * @param array      $data
     * @return void
     * @throws \Throwable
     */
    protected function insert(Connection $connection, string $table, array $data)
    {
        $connection->transactional(
            function ($connection) use ($table, $data) {
                $connection->executeStatement('SET foreign_key_checks = 0');

                foreach ($data as $entry) {
                    $connection->insert(
                        $table,
                        array_map(
                            function ($id) {
                                return Uuid::fromHexToBytes($id);
                            },
                            $entry
                        )
                    );
                }

                $connection->executeStatement('SET foreign_key_checks = 1');
            }
        );
    }

    protected function createMatrix(
        string $leftKey,
        array  $leftIds,
        string $rightKey,
        array  $rightIds
    ): array
    {
        $output = [];

        foreach ($leftIds as $leftId) {
            foreach ($rightIds as $rightId) {
                $output[] = [
                    $leftKey  => $leftId,
                    $rightKey => $rightId,
                ];
            }
        }

        return $output;
    }
}
