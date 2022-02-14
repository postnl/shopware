<?php declare(strict_types=1);

namespace PostNL\Shipments\Component\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shipments\Entity\Product\Aggregate\ProductTranslation\ProductTranslationDefinition;
use PostNL\Shipments\Entity\Product\ProductDefinition;
use PostNL\Shipments\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shipments\Service\PostNL\Delivery\Zone\Zone;
use Shopware\Core\Framework\Migration\MigrationStep;

abstract class ProductMigration extends MigrationStep
{
    use MigrationLocaleTrait;

    /**
     * @param Connection $connection
     * @param array<mixed> $products
     * @return void
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function insertProducts(Connection $connection, array $products): void
    {
        $languages = $this->getOrCreateLanguages($connection);
        $productCodeIdField = ProductDefinition::ENTITY_NAME . '_id';

        $connection->beginTransaction();

        try {
            foreach ($products as $product) {
                $name = $product['name'];
                unset($product['name']);

                $connection->insert(ProductDefinition::ENTITY_NAME, $product);

                foreach ($languages as $locale => $language) {
                    $connection->insert(ProductTranslationDefinition::ENTITY_NAME, [
                        'name' => $name,
                        'description' => $this->buildProductDescription($product, $locale),
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

    /**
     * @param Connection $connection
     * @param array<string> $deleteIds
     * @return void
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteProducts(Connection $connection, array $deleteIds): void
    {
        $connection->beginTransaction();

        try {
            foreach ($deleteIds as $id) {
                $connection->delete(ProductDefinition::ENTITY_NAME, ['id' => $id]);
            }

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }


    /**
     * @param array<mixed> $product
     * @param string $locale
     * @return string
     */
    private function buildProductDescription(array $product, string $locale): string
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
                ProductDefinition::STOR_HOME_ALONE => 'no delivery to neighbors', //TODO naam veranderen
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 'return if no answer',
                ProductDefinition::STOR_INSURANCE => 'insured',
                ProductDefinition::STOR_SIGNATURE => 'signature on delivery',
                ProductDefinition::STOR_AGE_CHECK => '18+ check',
                ProductDefinition::STOR_NOTIFICATION => 'notification when available for pickup',
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
                ProductDefinition::STOR_HOME_ALONE => 'keine Lieferung an Nachbarn', //TODO naam veranderen
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 'Rücksendung bei Nichtbeantwortung',
                ProductDefinition::STOR_INSURANCE => 'versichert',
                ProductDefinition::STOR_SIGNATURE => 'Unterschrift bei Lieferung',
                ProductDefinition::STOR_AGE_CHECK => '18+ Kontrolle',
                ProductDefinition::STOR_NOTIFICATION => 'Benachrichtigung bei Abholbereitschaft',
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
                ProductDefinition::STOR_HOME_ALONE => 'niet bij buren bezorgen', //TODO naam veranderen
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 'retour bij geen gehoor',
                ProductDefinition::STOR_INSURANCE => 'verzekerd',
                ProductDefinition::STOR_SIGNATURE => 'handtekening voor ontvangst',
                ProductDefinition::STOR_AGE_CHECK => '18+ check',
                ProductDefinition::STOR_NOTIFICATION => 'notificatie wanneer beschikbaar voor ophalen',
            ],
        ];

        $parts = [];
        if (in_array($product['destination_zone'], [Zone::NL, Zone::BE])) {
            $parts[] = $translations[$locale]['delivery_type'][$product['delivery_type']];
        } else {
            $parts[] = $translations[$locale]['destination_zone'][$product['destination_zone']];
        }

        foreach([
            ProductDefinition::STOR_HOME_ALONE,
            ProductDefinition::STOR_RETURN_IF_NOT_HOME,
            ProductDefinition::STOR_INSURANCE,
            ProductDefinition::STOR_SIGNATURE,
            ProductDefinition::STOR_AGE_CHECK,
            ProductDefinition::STOR_NOTIFICATION,
        ] as $flag) {
            if ($product[$flag] === true) {
                $parts[] = $translations[$locale][$flag];
            }
        }

        return implode(', ', $parts);
    }
}
