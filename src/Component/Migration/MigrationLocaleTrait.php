<?php

namespace PostNl\Shipments\Component\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Swag\LanguagePack\SwagLanguagePack;
use Swag\LanguagePack\Util\Exception\MissingLocalesException;

trait MigrationLocaleTrait
{

    public function getOrCreateLanguages(Connection $connection): array
    {
        $localeCodes = ['en-GB', 'de-DE', 'nl-NL'];
        $locales = $this->getLocales($connection, $localeCodes);
        $data = $this->createLanguageData($connection, $locales);

        $languages = [];
        $newLanguages = [];
        foreach ($data as $locale) {
            $language = [
                'id' => Uuid::randomBytes(),
                'languageId' => $locale['languageId'],
            ];

            if ($locale['languageId'] === null) {
                $newLanguageId = Uuid::randomBytes();
                $language['languageId'] = $newLanguageId;

                $newLanguages[] = [
                    'id' => $newLanguageId,
                    'name' => $locale['name'],
                    'localeId' => $locale['id'],
                    'translationCodeId' => $locale['id'],
                ];
            }

            $languages[] = $language;
        }

        $insertLanguagesSql = <<<SQL
INSERT INTO `language` (`id`, `name`, `locale_id`, `translation_code_id`, `created_at`)
VALUES (:id, :name, :localeId, :translationCodeId, NOW());
SQL;

        foreach ($newLanguages as $newLanguage) {
            $connection->executeStatement($insertLanguagesSql, $newLanguage);
        }

        return $languages;
    }

    private function createLanguageData(Connection $connection, array $locales): array
    {
        $sql = <<<SQL
SELECT lang.`id` as id, loc.`code` as code
FROM `language` lang
LEFT JOIN `locale` loc ON loc.`id` = lang.`translation_code_id`
WHERE loc.`code` IN (?)
SQL;

        $existingLanguages = $connection->executeQuery(
            $sql,
            [\array_keys($locales)],
            [Connection::PARAM_STR_ARRAY]
        )->fetchAll();

        return \array_map(static function ($locale) use ($existingLanguages): array {
            $languageId = null;
            foreach ($existingLanguages as $language) {
                if ($locale['code'] === $language['code']) {
                    $languageId = $language['id'];

                    break;
                }
            }
            $locale['languageId'] = $languageId;

            return $locale;
        }, $locales);
    }

    private function getLocales(Connection $connection, array $localeCodes): array
    {
        $sql = <<<SQL
SELECT `id`, `code` FROM `locale` WHERE `code` IN (?);
SQL;

        return $connection->executeQuery(
            $sql,
            [\array_values($localeCodes)],
            [Connection::PARAM_STR_ARRAY]
        )->fetchAll();
    }
}
