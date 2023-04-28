<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\ProductMigration;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Entity\Product\ProductDefinition;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1678710150CreateV2_0InternationalMailboxProducts extends ProductMigration
{
    public function getCreationTimestamp(): int
    {
        return 1678710150;
    }

    public function update(Connection $connection): void
    {
        $products = [
            // EU Mailbox
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_MAILBOX_NL_EU_6440),
                'product_code_delivery'                        => '6440',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::EU,
                'delivery_type'                                => DeliveryType::MAILBOX,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => null,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 0,
            ],
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_MAILBOX_NL_EU_6972),
                'product_code_delivery'                        => '6972',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::EU,
                'delivery_type'                                => DeliveryType::MAILBOX,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => null,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 1,
            ],
            // GLOBAL Mailbox
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_MAILBOX_NL_GLOBAL_6440),
                'product_code_delivery'                        => '6440',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::GLOBAL,
                'delivery_type'                                => DeliveryType::MAILBOX,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => null,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 0,
            ],
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_MAILBOX_NL_GLOBAL_6972),
                'product_code_delivery'                        => '6972',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::GLOBAL,
                'delivery_type'                                => DeliveryType::MAILBOX,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => null,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 1,
            ],
            // EU Package
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_PACKAGE_NL_EU_6405),
                'product_code_delivery'                        => '6405',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::EU,
                'delivery_type'                                => DeliveryType::PACKAGE,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 0,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 0,
            ],
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_PACKAGE_NL_EU_6350),
                'product_code_delivery'                        => '6350',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::EU,
                'delivery_type'                                => DeliveryType::PACKAGE,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 0,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 1,
            ],
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_PACKAGE_NL_EU_6906),
                'product_code_delivery'                        => '6906',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::EU,
                'delivery_type'                                => DeliveryType::PACKAGE,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 1,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 1,
            ],
            // GLOBAL Package
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_PACKAGE_NL_GLOBAL_6405),
                'product_code_delivery'                        => '6405',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::GLOBAL,
                'delivery_type'                                => DeliveryType::PACKAGE,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 0,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 0,
            ],
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_PACKAGE_NL_GLOBAL_6350),
                'product_code_delivery'                        => '6350',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::GLOBAL,
                'delivery_type'                                => DeliveryType::PACKAGE,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 0,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 1,
            ],
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_PACKAGE_NL_GLOBAL_6906),
                'product_code_delivery'                        => '6906',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::GLOBAL,
                'delivery_type'                                => DeliveryType::PACKAGE,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 1,
                ProductDefinition::STOR_INSURANCE_PLUS         => null,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => 1,
            ],
        ];

        $this->insertProducts($connection, $products);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
