<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\OptionMigration;
use PostNL\Shopware6\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1674138063CreateV2_0Options extends OptionMigration
{
    public function getCreationTimestamp(): int
    {
        return 1674138063;
    }

    public function update(Connection $connection): void
    {
        $options = [
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_118_006),
                'name' => [
                    'en-GB' => 'Evening delivery',
                    'de-DE' => 'Abendlieferung',
                    'nl-NL' => 'Avondbezorging',
                ],
                'description' => [
                    'en-GB' => 'Show timeframes for evening delivery',
                    'de-DE' => 'Zeitrahmen für Abendlieferung anzeigen',
                    'nl-NL' => 'Toon bezorgopties voor avondbezorging',
                ],
                'characteristic' => '118',
                'option' => '006',
            ],
        ];

        $this->insertOptions($connection, $options);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
