<?php declare(strict_types=1);

namespace PostNL\Shipments\Component\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shipments\Entity\ProductCode\Aggregate\ProductCodeConfigTranslation\ProductCodeConfigTranslationDefinition;
use PostNL\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
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
                $connection->delete(ProductCodeConfigDefinition::ENTITY_NAME, ['id' => $id]);
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
    private function buildProductName(array $product, string $locale): string
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
                ProductCodeConfigDefinition::STOR_HOME_ALONE => 'no delivery to neighbors', //TODO naam veranderen
                ProductCodeConfigDefinition::STOR_RETURN_IF_NOT_HOME => 'return if no answer',
                ProductCodeConfigDefinition::STOR_INSURANCE => 'insured',
                ProductCodeConfigDefinition::STOR_SIGNATURE => 'signature on delivery',
                ProductCodeConfigDefinition::STOR_AGE_CHECK => '18+ check',
                ProductCodeConfigDefinition::STOR_NOTIFICATION => 'notification when available for pickup',
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
                ProductCodeConfigDefinition::STOR_HOME_ALONE => 'keine Lieferung an Nachbarn', //TODO naam veranderen
                ProductCodeConfigDefinition::STOR_RETURN_IF_NOT_HOME => 'Rücksendung bei Nichtbeantwortung',
                ProductCodeConfigDefinition::STOR_INSURANCE => 'versichert',
                ProductCodeConfigDefinition::STOR_SIGNATURE => 'Unterschrift bei Lieferung',
                ProductCodeConfigDefinition::STOR_AGE_CHECK => '18+ Kontrolle',
                ProductCodeConfigDefinition::STOR_NOTIFICATION => 'Benachrichtigung bei Abholbereitschaft',
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
                ProductCodeConfigDefinition::STOR_HOME_ALONE => 'niet bij buren bezorgen', //TODO naam veranderen
                ProductCodeConfigDefinition::STOR_RETURN_IF_NOT_HOME => 'retour bij geen gehoor',
                ProductCodeConfigDefinition::STOR_INSURANCE => 'verzekerd',
                ProductCodeConfigDefinition::STOR_SIGNATURE => 'handtekening voor ontvangst',
                ProductCodeConfigDefinition::STOR_AGE_CHECK => '18+ check',
                ProductCodeConfigDefinition::STOR_NOTIFICATION => 'notificatie wanneer beschikbaar voor ophalen',
            ],
        ];

        $nameParts = [];
        if (in_array($product['destination_zone'], [Zone::NL, Zone::BE])) {
            $nameParts[] = $translations[$locale]['delivery_type'][$product['delivery_type']];
        } else {
            $nameParts[] = $translations[$locale]['destination_zone'][$product['destination_zone']];
        }

        if ($product[ProductCodeConfigDefinition::STOR_HOME_ALONE] === true) {
            $nameParts[] = $translations[$locale][ProductCodeConfigDefinition::STOR_HOME_ALONE];
        }
        if ($product[ProductCodeConfigDefinition::STOR_RETURN_IF_NOT_HOME] === true) {
            $nameParts[] = $translations[$locale][ProductCodeConfigDefinition::STOR_RETURN_IF_NOT_HOME];
        }
        if ($product[ProductCodeConfigDefinition::STOR_INSURANCE] === true) {
            $nameParts[] = $translations[$locale][ProductCodeConfigDefinition::STOR_INSURANCE];
        }
        if ($product[ProductCodeConfigDefinition::STOR_SIGNATURE] === true) {
            $nameParts[] = $translations[$locale][ProductCodeConfigDefinition::STOR_SIGNATURE];
        }
        if ($product[ProductCodeConfigDefinition::STOR_AGE_CHECK] === true) {
            $nameParts[] = $translations[$locale][ProductCodeConfigDefinition::STOR_AGE_CHECK];
        }
        if ($product[ProductCodeConfigDefinition::STOR_NOTIFICATION] === true) {
            $nameParts[] = $translations[$locale][ProductCodeConfigDefinition::STOR_NOTIFICATION];
        }


        return implode(', ', $nameParts);
    }
}
