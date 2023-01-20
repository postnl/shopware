<?php declare(strict_types=1);

namespace PostNL\Shopware6\Component\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use PostNL\Shopware6\Entity\Option\Aggregate\OptionTranslation\OptionTranslationDefinition;
use PostNL\Shopware6\Entity\Option\OptionDefinition;
use Shopware\Core\Defaults as ShopwareDefaults;
use Shopware\Core\Framework\Migration\MigrationStep;

abstract class OptionMigration extends MigrationStep
{
    use MigrationLocaleTrait;
    use MigrationQuoteTrait;

    /**
     * @param Connection $connection
     * @param array      $options
     * @return void
     * @throws ConnectionException
     * @throws Exception
     */
    public function insertOptions(Connection $connection, array $options): void
    {
        $languages = $this->getOrCreateLanguages($connection);
        $OptionIdField = OptionDefinition::ENTITY_NAME . '_id';

        $connection->beginTransaction();

        try {
            foreach ($options as $option) {
                $name = $option['name'];
                unset($option['name']);
                if (!is_array($name)) {
                    $name = ['en-GB' => $name];
                }
                if (!array_key_exists('en-GB', $name)) {
                    $name['en-GB'] = current($name);
                }

                $description = $option['description'];
                unset($option['description']);
                if (!is_array($description)) {
                    $description = ['en-GB' => $description];
                }
                if (!array_key_exists('en-GB', $description)) {
                    $description['en-GB'] = current($description);
                }

                if (!array_key_exists('created_at', $option)) {
                    $option['created_at'] = (new \DateTime())->format(ShopwareDefaults::STORAGE_DATE_TIME_FORMAT);
                }

                $connection->insert(OptionDefinition::ENTITY_NAME, $this->quote($connection, $option));

                foreach ($languages as $locale => $language) {
                    $connection->insert(OptionTranslationDefinition::ENTITY_NAME, $this->quote($connection, [
                        'name'         => $name[$locale] ?? $name['en-GB'],
                        'description'  => $description[$locale] ?? $description['en-GB'],
                        'language_id'  => $language['id'],
                        $OptionIdField => $option['id'],
                        'created_at'   => $option['created_at'],
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
     * @param Connection    $connection
     * @param array<string> $deleteIds
     * @return void
     * @throws ConnectionException
     * @throws Exception
     */
    public function deleteOptions(Connection $connection, array $deleteIds): void
    {
        $connection->beginTransaction();

        try {
            foreach ($deleteIds as $id) {
                $connection->delete(OptionDefinition::ENTITY_NAME, ['id' => $id]);
            }

            $connection->commit();
        }
        catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}
