<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\OptionMigration;
use PostNL\Shopware6\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1678290815CreateV2_0InternationalOptions extends OptionMigration
{
    public function getCreationTimestamp(): int
    {
        return 1678290815;
    }

    public function update(Connection $connection): void
    {
        $options = [
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_101_012),
                'name' => [
                    'en-GB' => 'Parcels EU',
                    'de-DE' => 'Parcels EU',
                    'nl-NL' => 'Parcels EU',
                ],
                'description' => [
                    'en-GB' => 'Required option for Parcels EU',
                    'de-DE' => 'Obligatorische Option für Parcels EU',
                    'nl-NL' => 'Vereiste optie voor Parcels EU',
                ],
                'characteristic' => '101',
                'option' => '012',
                'hidden' => 1,
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_005_025),
                'name' => [
                    'en-GB' => 'Track & Trace',
                    'de-DE' => 'Track & Trace',
                    'nl-NL' => 'Track & Trace',
                ],
                'description' => [
                    'en-GB' => 'Send with Track & Trace',
                    'de-DE' => 'Senden mit Track & Trace',
                    'nl-NL' => 'Verzenden met Track & Trace',
                ],
                'characteristic' => '005',
                'option' => '025',
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_004_015),
                'name' => [
                    'en-GB' => 'Track & Trace Insured',
                    'de-DE' => 'Track & Trace Versichert',
                    'nl-NL' => 'Track & Trace Verzekerd',
                ],
                'description' => [
                    'en-GB' => 'Send with Track & Trace, insured up to 50 euro',
                    'de-DE' => 'Senden mit Track & Trace, bis zu 50 Euro versichert',
                    'nl-NL' => 'Verzenden met Track & Trace, verzekerd tot 50 euro',
                ],
                'characteristic' => '004',
                'option' => '015',
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_004_016),
                'name' => [
                    'en-GB' => 'Track & Trace Insured Plus',
                    'de-DE' => 'Track & Trace Versichert Plus',
                    'nl-NL' => 'Track & Trace Verzekerd Plus',
                ],
                'description' => [
                    'en-GB' => 'Send with Track & Trace, insured up to 500 euro',
                    'de-DE' => 'Senden mit Track & Trace, bis zu 500 Euro versichert',
                    'nl-NL' => 'Verzenden met Track & Trace, verzekerd tot 500 euro',
                ],
                'characteristic' => '004',
                'option' => '016',
            ],
        ];

        $this->insertOptions($connection, $options);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
