<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Defaults;
use Shopware\Core\Checkout\Shipping\Aggregate\ShippingMethodTranslation\ShippingMethodTranslationDefinition;
use Shopware\Core\Checkout\Shipping\ShippingMethodDefinition;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1715095320AddTechnicalNameToShippingMethods extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1715095320;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->beginTransaction();

            $fetchQB = $connection->createQueryBuilder();
            $updateShippingMethodQB = $connection->createQueryBuilder();
            $updateTranslationQB = $connection->createQueryBuilder();

            $fetchQB
                ->select('sm.id', 'sm.technical_name', 'smt.language_id', 'smt.custom_fields')
                ->from(ShippingMethodDefinition::ENTITY_NAME, 'sm')
                ->join(
                    'sm',
                    ShippingMethodTranslationDefinition::ENTITY_NAME,
                    'smt',
                    'sm.id = smt.shipping_method_id'
                )
                ->where('smt.custom_fields LIKE "%postnl%"')
                ->andWhere('sm.technical_name IS NULL');

            $updateShippingMethodQB
                ->update(ShippingMethodDefinition::ENTITY_NAME, 'sm')
                ->set('sm.technical_name', ':technical_name')
                ->where('sm.id = :id');

            $updateTranslationQB
                ->update(ShippingMethodTranslationDefinition::ENTITY_NAME, 'smt')
                ->set('smt.custom_fields', ':custom_fields')
                ->where('smt.shipping_method_id = :shipping_method_id')
                ->andWhere('smt.language_id = :language_id');

            $rows = $fetchQB->fetchAllAssociative();

            foreach($rows as $row) {
                $customFields = json_decode($row['custom_fields'], true);
                $deliveryType = $customFields[Defaults::CUSTOM_FIELDS_KEY]['deliveryType'];
                unset($customFields[Defaults::CUSTOM_FIELDS_KEY]);

                $customFields = (!empty($customFields))
                    ? json_encode($customFields)
                    : null;

                $updateShippingMethodQB
                    ->setParameters(
                        [
                            'id' => $row['id'],
                            'technical_name' => 'postnl_'.strtolower($deliveryType),
                        ]
                    )
                    ->executeStatement();

                $updateTranslationQB
                    ->setParameters(
                        [
                            'shipping_method_id' => $row['id'],
                            'language_id' => $row['language_id'],
                            'custom_fields' => $customFields,
                        ]
                    )
                    ->executeStatement();
            }

            $connection->commit();
        }
        catch (\Exception $e) {
            $connection->rollBack();

            throw $e;
        }
    }
}
