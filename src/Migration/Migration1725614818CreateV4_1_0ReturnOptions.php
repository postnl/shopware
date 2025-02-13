<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\OptionMigration;
use PostNL\Shopware6\Defaults;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @internal
 */
#[Package('core')]
class Migration1725614818CreateV4_1_0ReturnOptions extends OptionMigration
{
    public function getCreationTimestamp(): int
    {
        return 1725614818;
    }

    public function update(Connection $connection): void
    {
        $options = [
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_152_028),
                'name' => [
                    'en-GB' => 'Label in the box',
                    'de-DE' => 'Label im Paket',
                    'nl-NL' => 'Label in de doos',
                ],
                'description' => [
                    'en-GB' => 'Prints an extra label for returns',
                    'de-DE' => 'Druckt ein zusätzliches Etikett für Rücksendungen',
                    'nl-NL' => 'Drukt een extra label af voor retourzendingen',
                ],
                'characteristic' => '152',
                'option' => '028',
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_152_025),
                'name' => [
                    'en-GB' => 'Smart returns',
                    'de-DE' => 'Intelligente Rücksendungen',
                    'nl-NL' => 'Smart returns',
                ],
                'description' => [
                    'en-GB' => 'Generates a barcode that can be scanned at a delivery point',
                    'de-DE' => 'Erzeugt einen Barcode, der an einer Lieferstelle gescannt werden kann',
                    'nl-NL' => 'Genereert een barcode die kan worden gescand op een afleverpunt',
                ],
                'characteristic' => '152',
                'option' => '025',
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_152_026),
                'name' => [
                    'en-GB' => '"Heen en terug" label',
                    'de-DE' => 'Etikett "Heen en terug"',
                    'nl-NL' => '"Heen en terug" label',
                ],
                'description' => [
                    'en-GB' => 'The shipment label can also be used for returns',
                    'de-DE' => 'Das Versandetikett kann auch für Rücksendungen verwendet werden',
                    'nl-NL' => 'Het verzendlabel kan ook worden gebruikt voor retourzendingen',
                ],
                'characteristic' => '152',
                'option' => '026',
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_191_001),
                'name' => [
                    'en-GB' => '35 day return window',
                    'de-DE' => '35 Tage Rückgabefrist',
                    'nl-NL' => 'Retourtermijn van 35 dagen',
                ],
                'description' => [
                    'en-GB' => 'Sets the return window to 35 days, instead of the default 20 days',
                    'de-DE' => 'Legt das Rückgabefenster auf 35 Tage fest, anstelle der standardmäßigen 20 Tage',
                    'nl-NL' => 'Stelt de retourtermijn in op 35 dagen, in plaats van de standaard 20 dagen',
                ],
                'characteristic' => '191',
                'option' => '001',
            ],
            [
                'id' => Uuid::fromHexToBytes(Defaults::OPTION_191_004),
                'name' => [
                    'en-GB' => '"Heen en terug" requires activation',
                    'de-DE' => '"Heen en terug"-Block',
                    'nl-NL' => '"Heen en terug" vereist activatie',
                ],
                'description' => [
                    'en-GB' => 'The "Heen en terug" label cannot be immediately used for returns',
                    'de-DE' => 'Das Etikett "Heen en terug" kann nicht sofort für Rücksendungen verwendet werden.',
                    'nl-NL' => 'Het “Heen en terug” label kan niet direct worden gebruikt voor retourzendingen',
                ],
                'characteristic' => '191',
                'option' => '004',
            ],
        ];

        $this->insertOptions($connection, $options);
    }
}
