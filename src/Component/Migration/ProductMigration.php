<?php declare(strict_types=1);

namespace PostNL\Shopware6\Component\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Entity\Product\Aggregate\ProductTranslation\ProductTranslationDefinition;
use PostNL\Shopware6\Entity\Product\ProductDefinition;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use Shopware\Core\Defaults as ShopwareDefaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

abstract class ProductMigration extends MigrationStep
{
    use MigrationLocaleTrait;
    use MigrationQuoteTrait;

    /**
     * @param Connection   $connection
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
                if (array_key_exists('name', $product)) {
                    $name = $product['name'];
                    unset($product['name']);
                }

                if (!array_key_exists('created_at', $product)) {
                    $product['created_at'] = (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT);
                }

                $connection->insert(ProductDefinition::ENTITY_NAME, $this->quote($connection, $product));

                foreach ($languages as $locale => $language) {
                    $connection->insert(ProductTranslationDefinition::ENTITY_NAME, $this->quote($connection, [
                        'name'              => $name ?? $this->buildProductDescription($product, $locale),
                        'description'       => $this->buildProductDescription($product, $locale),
                        'language_id'       => $language['id'],
                        $productCodeIdField => $product['id'],
                        'created_at'        => $product['created_at'],
                    ]));
                }
            }

            $connection->commit();
        }
        catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    /**
     * @param Connection            $connection
     * @param array<string, string> $deleteIds
     * @return void
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function deprecateProducts(Connection $connection, array $deleteIds): void
    {
        $connection->beginTransaction();

        try {
            foreach ($deleteIds as $oldId => $replaceId) {
                $connection->update(
                    'postnl_product',
                    [
                        'replaced_by_id' => Uuid::fromHexToBytes($replaceId),
                    ],
                    [
                        'id' => Uuid::fromHexToBytes($oldId),
                    ]
                );
            }

            $connection->commit();
        }
        catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }


    /**
     * @param array<mixed> $product
     * @param string       $locale
     * @return string
     */
    private function buildProductDescription(array $product, string $locale): string
    {
        $translations = [
            'en-GB' => [
                'destination_zone'                         => [
                    Zone::NL     => 'Netherlands',
                    Zone::BE     => 'Belgium',
                    Zone::EU     => 'Europe',
                    Zone::GLOBAL => 'International',
                ],
                'delivery_type'                            => [
                    DeliveryType::SHIPMENT => 'Standard Shipment',
                    DeliveryType::PICKUP   => 'Pick-up at PostNL location',
                    DeliveryType::MAILBOX  => 'Mailbox parcel',
                    DeliveryType::PACKAGE  => 'Parcel',
                ],
                ProductDefinition::STOR_HOME_ALONE         => 'Deliver to stated address only',
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 'Return when not home',
                ProductDefinition::STOR_INSURANCE          => 'Extra Cover',
                ProductDefinition::STOR_INSURANCE_PLUS     => 'Extra Cover Plus',
                ProductDefinition::STOR_SIGNATURE          => 'Signature on delivery',
                ProductDefinition::STOR_AGE_CHECK          => 'Age check',
                ProductDefinition::STOR_NOTIFICATION       => 'Notification',
                ProductDefinition::STOR_TRACK_AND_TRACE    => 'Track and Trace',
            ],
            'de-DE' => [
                'destination_zone'                         => [
                    Zone::NL     => 'Niederlande',
                    Zone::BE     => 'Belgien',
                    Zone::EU     => 'Europa',
                    Zone::GLOBAL => 'International',
                ],
                'delivery_type'                            => [
                    DeliveryType::SHIPMENT => 'Standardversand',
                    DeliveryType::PICKUP   => 'Abholung am PostNL-Standort',
                    DeliveryType::MAILBOX  => 'Briefkasten-Paket',
                    DeliveryType::PACKAGE  => 'Paket',
                ],
                ProductDefinition::STOR_HOME_ALONE         => 'Nur an die angegebene Adresse liefern',
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 'Zurück, wenn nicht zu Hause sein',
                ProductDefinition::STOR_INSURANCE          => 'Zusätzliche Abdeckung',
                ProductDefinition::STOR_INSURANCE_PLUS     => 'Zusätzliche Abdeckung Plus',
                ProductDefinition::STOR_SIGNATURE          => 'Unterschrift bei Lieferung',
                ProductDefinition::STOR_AGE_CHECK          => 'Altersprüfung',
                ProductDefinition::STOR_NOTIFICATION       => 'Benachrichtigung',
                ProductDefinition::STOR_TRACK_AND_TRACE    => 'Track and Trace',
            ],
            'nl-NL' => [
                'destination_zone'                         => [
                    Zone::NL     => "Nederland",
                    Zone::BE     => "België",
                    Zone::EU     => "Europa",
                    Zone::GLOBAL => "Internationaal",
                ],
                'delivery_type'                            => [
                    DeliveryType::SHIPMENT => 'Standaard zending',
                    DeliveryType::PICKUP   => 'Ophalen bij een PostNL-punt',
                    DeliveryType::MAILBOX  => 'Brievenbuspakje',
                    DeliveryType::PACKAGE  => 'Pakje',
                ],
                ProductDefinition::STOR_HOME_ALONE         => 'Alleen huisadres',
                ProductDefinition::STOR_RETURN_IF_NOT_HOME => 'Retour bij geen gehoor',
                ProductDefinition::STOR_INSURANCE          => 'Verhoogd aansprakelijkheid',
                ProductDefinition::STOR_INSURANCE_PLUS     => 'Verhoogd aansprakelijkheid Plus',
                ProductDefinition::STOR_SIGNATURE          => 'Handtekening voor ontvangst',
                ProductDefinition::STOR_AGE_CHECK          => 'Leeftijdscontrole',
                ProductDefinition::STOR_NOTIFICATION       => 'Notificatie',
                ProductDefinition::STOR_TRACK_AND_TRACE    => 'Track and Trace',
            ],
        ];

        $parts = [];

        if(in_array($product['destination_zone'], $translations[$locale]['destination_zone'])) {
            $parts[] = $translations[$locale]['destination_zone'][$product['destination_zone']];
        }

        if(in_array($product['delivery_type'], $translations[$locale]['delivery_type'])) {
            $parts[] = $translations[$locale]['delivery_type'][$product['delivery_type']];
        }

        foreach (array_keys(ProductDefinition::ALL_FLAGS) as $flag) {
            if (isset($product[$flag]) && boolval($product[$flag]) === true) {
                $parts[] = $translations[$locale][$flag];
            }
        }

        return implode(', ', $parts);
    }
}
