<?php declare(strict_types=1);

namespace PostNl\Shipments\Migration;

use Doctrine\DBAL\Connection;
use PostNl\Shipments\Component\Migration\MigrationLocaleTrait;
use PostNl\Shipments\Defaults;
use PostNl\Shipments\Entity\ProductCode\Aggregate\ProductCodeConfigTranslation\ProductCodeConfigTranslationDefinition;
use PostNl\Shipments\Entity\ProductCode\ProductCodeConfigDefinition;
use PostNl\Shipments\Service\PostNL\Product\Code\DeliveryType;
use PostNl\Shipments\Service\PostNL\Product\Code\Zone\Zone;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1640162702CreateV1Products extends MigrationStep
{
    use MigrationLocaleTrait;

    public function getCreationTimestamp(): int
    {
        return 1640162702;
    }

    public function update(Connection $connection): void
    {
        $languages = $this->getOrCreateLanguages($connection);
        $enId = $languages['en-GB']['id'];
        $deId = $languages['de-DE']['id'];
        $nlId = $languages['nl-NL']['id'];

        $productCodeIdField = ProductCodeConfigDefinition::ENTITY_NAME . '_id';

        $products = [
            [
                'id' => Defaults::PRODUCT_SHIPPING_NL_NL_3085,
                'name' => [
                    'en-GB' => 'Standard shipment',
                    'de-DE' => 'Standard-Versand',
                    'nl-NL' => 'Standaard verzending',
                ],
                'product_code_delivery' => '3085',
                'source_zone' => Zone::NL,
                'destination_zone' => Zone::NL,
                'delivery_type' => DeliveryType::SHIPMENT,
                'next_door_delivery' => true,
                'return_if_not_home' => false,
                'insurance' => false,
                'signature' => false,
                'age_check' => false,
                'notification' => false,
                'created_at' => (new \DateTime())->format(\Shopware\Core\Defaults::STORAGE_DATE_TIME_FORMAT)
            ]
        ];

        $connection->beginTransaction();

        try {
            foreach($products as $product) {
                $name = $product['name'];
                unset($product['name']);

                $connection->insert(ProductCodeConfigDefinition::ENTITY_NAME, $product);

                foreach($languages as $locale => $language) {
                    $connection->insert(ProductCodeConfigTranslationDefinition::ENTITY_NAME, [
                        'name' => $name[$locale],
                        'languageId' => $language['id'],
                        $productCodeIdField => $product['id'],
                        'created_at' => $product['created_at'],
                    ]);
                }
            }

            $connection->commit();
        } catch (\Exception $e ) {
            $connection->rollBack();
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        $deleteIds = [
            Defaults::PRODUCT_SHIPPING_NL_NL_3085
        ];

        $connection->beginTransaction();

        try {
            foreach($deleteIds as $id) {
                $connection->delete(ProductCodeConfigDefinition::ENTITY_NAME, ['id' => $id]);
            }
            
            $connection->commit();
        } catch (\Exception $e ) {
            $connection->rollBack();
        }
    }

}
