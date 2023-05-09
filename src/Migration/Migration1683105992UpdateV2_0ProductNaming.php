<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\ProductMigration;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1683105992UpdateV2_0ProductNaming extends ProductMigration
{
    public function getCreationTimestamp(): int
    {
        return 1683105992;
    }

    public function update(Connection $connection): void
    {
        $products = $this->getProductTranslations($connection);

        $products = array_map(function (array $product) {
            if($product['name'] === $product['description']) {
                unset($product['name']);
            }

            unset($product['description']);

            return $product;
        }, $products);

        $this->updateProductTranslations(
            $connection,
            $products
        );
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
