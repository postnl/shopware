<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\ProductMigration;
use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Entity\Product\ProductDefinition;
use PostNL\Shopware6\Service\PostNL\Delivery\DeliveryType;
use PostNL\Shopware6\Service\PostNL\Delivery\Zone\Zone;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1678290809CreateV2_0InternationalProducts extends ProductMigration
{
    public function getCreationTimestamp(): int
    {
        return 1678290809;
    }

    public function update(Connection $connection): void
    {
        $products = [
            [
                'id'                                           => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_EU_4907_005_025),
                'product_code_delivery'                        => '4907',
                'source_zone'                                  => Zone::NL,
                'destination_zone'                             => Zone::EU,
                'delivery_type'                                => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 0,
                ProductDefinition::STOR_INSURANCE_PLUS         => 0,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => null,
                ProductDefinition::STOR_MAILBOX_LARGER_PACKAGE => null,
            ],
            [
                'id'                                       => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_BE_EU_4907_005_025),
                'product_code_delivery'                    => '4907',
                'source_zone'                              => Zone::BE,
                'destination_zone'                         => Zone::EU,
                'delivery_type'                            => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 0,
                ProductDefinition::STOR_INSURANCE_PLUS         => 0,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => null,
                ProductDefinition::STOR_MAILBOX_LARGER_PACKAGE => null,
            ],
            [
                'id'                                       => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_NL_GLOBAL_4909_005_025),
                'product_code_delivery'                    => '4909',
                'source_zone'                              => Zone::NL,
                'destination_zone'                         => Zone::GLOBAL,
                'delivery_type'                            => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 0,
                ProductDefinition::STOR_INSURANCE_PLUS         => 0,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => null,
                ProductDefinition::STOR_MAILBOX_LARGER_PACKAGE => null,
            ],
            [
                'id'                                       => Uuid::fromHexToBytes(Defaults::PRODUCT_SHIPPING_BE_GLOBAL_4909_005_025),
                'product_code_delivery'                    => '4909',
                'source_zone'                              => Zone::BE,
                'destination_zone'                         => Zone::GLOBAL,
                'delivery_type'                            => DeliveryType::SHIPMENT,
                ProductDefinition::STOR_HOME_ALONE             => null,
                ProductDefinition::STOR_RETURN_IF_NOT_HOME     => null,
                ProductDefinition::STOR_INSURANCE              => 0,
                ProductDefinition::STOR_INSURANCE_PLUS         => 0,
                ProductDefinition::STOR_SIGNATURE              => null,
                ProductDefinition::STOR_AGE_CHECK              => null,
                ProductDefinition::STOR_NOTIFICATION           => null,
                ProductDefinition::STOR_TRACK_AND_TRACE        => null,
                ProductDefinition::STOR_MAILBOX_LARGER_PACKAGE => null,
            ],
        ];

        $this->insertProducts($connection, $products);

        $this->deprecateProducts($connection, [
            Defaults::PRODUCT_SHIPPING_NL_EU_4952     => Defaults::PRODUCT_SHIPPING_NL_EU_4907_005_025,
            Defaults::PRODUCT_SHIPPING_BE_EU_4952     => Defaults::PRODUCT_SHIPPING_BE_EU_4907_005_025,
            Defaults::PRODUCT_SHIPPING_NL_GLOBAL_4945 => Defaults::PRODUCT_SHIPPING_NL_GLOBAL_4909_005_025,
            Defaults::PRODUCT_SHIPPING_BE_GLOBAL_4945 => Defaults::PRODUCT_SHIPPING_BE_GLOBAL_4909_005_025,
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
