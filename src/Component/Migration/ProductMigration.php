<?php declare(strict_types=1);

namespace PostNL\Shipments\Component\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductCodeConfigTranslation\ProductCodeConfigTranslationDefinition;
use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
use PostNL\Shipments\Service\PostNL\Product\Code\DeliveryType;
use PostNL\Shipments\Service\PostNL\Product\Code\Zone\Zone;
use Shopware\Core\Framework\Migration\MigrationStep;

abstract class ProductMigration extends MigrationStep
{
    use MigrationLocaleTrait;

    public function insertProducts(Connection $connection, array $products): void
    {
        $languages = $this->getOrCreateLanguages($connection);
        $productCodeIdField = ProductCodeConfigDefinition::ENTITY_NAME . '_id';

        $connection->beginTransaction();

        try {
            foreach ($products as $product) {
                $connection->insert(ProductCodeConfigDefinition::ENTITY_NAME, $product);

                foreach ($languages as $locale => $language) {
                    $connection->insert(ProductCodeConfigTranslationDefinition::ENTITY_NAME, [
                        'name' => $this->buildProductName($product, $locale),
                        'language_id' => $language['id'],
                        $productCodeIdField => $product['id'],
                        'created_at' => $product['created_at'],
                    ]);
                }
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public function deleteProducts(Connection $connection, array $deleteIds): void
    {
        $connection->beginTransaction();

        try {
            foreach ($deleteIds as $id) {
                $connection->delete(ProductCodeConfigDefinition::ENTITY_NAME, ['id' => $id]);
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    private function buildProductName($product, $locale): string
    {
        $translations = [
            'en-GB' => [
                'destination_zone' => [
                    Zone::EU => 'ParcelsEU',
                    Zone::GLOBAL => 'Global Pack',
                ],
                'delivery_type' => [
                    DeliveryType::SHIPMENT => 'Standard shipping',
                    DeliveryType::PICKUP => 'PostNL Pickup Point',
                    DeliveryType::MAILBOX => 'Mailbox parcel',
                ],
                'next_door_delivery' => 'no delivery to neighbors',
                'return_if_not_home' => 'return if no answer',
                'insurance' => 'insured',
                'signature' => 'signature on delivery',
                'age_check' => '18+ check',
                'notification' => 'notification when available for pickup',
            ],
            'de-DE' => [
                'destination_zone' => [
                    Zone::EU => 'ParcelsEU',
                    Zone::GLOBAL => 'Global Pack',
                ],
                'delivery_type' => [
                    DeliveryType::SHIPMENT => 'Standardlieferung',
                    DeliveryType::PICKUP => 'PostNL Abholpunkt',
                    DeliveryType::MAILBOX => 'Briefkasten-Paket',
                ],
                'next_door_delivery' => 'keine Lieferung an Nachbarn',
                'return_if_not_home' => 'Rücksendung bei Nichtbeantwortung',
                'insurance' => 'versichert',
                'signature' => 'Unterschrift bei Lieferung',
                'age_check' => '18+ Kontrolle',
                'notification' => 'Benachrichtigung bei Abholbereitschaft',
            ],
            'nl-NL' => [
                'destination_zone' => [
                    Zone::EU => 'ParcelsEU',
                    Zone::GLOBAL => 'Global Pack',
                ],
                'delivery_type' => [
                    DeliveryType::SHIPMENT => 'Standaard verzending',
                    DeliveryType::PICKUP => 'PostNL Pickup Point',
                    DeliveryType::MAILBOX => 'Brievenbuspakje',
                ],
                'next_door_delivery' => 'niet bij buren bezorgen',
                'return_if_not_home' => 'retour bij geen gehoor',
                'insurance' => 'verzekerd',
                'signature' => 'handtekening voor ontvangst',
                'age_check' => '18+ check',
                'notification' => 'notificatie wanneer beschikbaar voor ophalen',
            ],
        ];

        $nameParts = [];
        if (in_array($product['destination_zone'], [Zone::NL, Zone::BE])) {
            $nameParts[] = $translations[$locale]['delivery_type'][$product['delivery_type']];
        } else {
            $nameParts[] = $translations[$locale]['destination_zone'][$product['destination_zone']];
        }

        if ($product['next_door_delivery'] === false) {
            $nameParts[] = $translations[$locale]['next_door_delivery'];
        }
        if ($product['return_if_not_home'] === true) {
            $nameParts[] = $translations[$locale]['return_if_not_home'];
        }
        if ($product['insurance'] === true) {
            $nameParts[] = $translations[$locale]['insurance'];
        }
        if ($product['signature'] === true) {
            $nameParts[] = $translations[$locale]['signature'];
        }
        if ($product['age_check'] === true) {
            $nameParts[] = $translations[$locale]['age_check'];
        }
        if ($product['notification'] === true) {
            $nameParts[] = $translations[$locale]['notification'];
        }


        return implode(', ', $nameParts);
    }
}
