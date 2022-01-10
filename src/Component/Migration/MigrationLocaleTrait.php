<?php declare(strict_types=1);

namespace PostNL\Shipments\Component\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;

trait MigrationLocaleTrait
{
    public function getOrCreateLanguages(Connection $connection): array
    {
        $localeCodes = [
            'English' => 'en-GB',
            'Deutsch' => 'de-DE',
            'Nederlands' => 'nl-NL'
        ];
        $locales = $this->getLocales($connection, $localeCodes);
        $data = $this->createLanguageData($connection, $locales);

        $languages = [];
        $newLanguages = [];
        foreach ($data as $locale) {
            $language = [
                'id' => $locale['languageId'],
                'name' => $locale['name'],
            ];

            if ($locale['languageId'] === null) {
                $newLanguageId = Uuid::randomBytes();
                $language['id'] = $newLanguageId;

                $newLanguages[] = [
                    'id' => $newLanguageId,
                    'name' => $locale['name'],
                    'localeId' => $locale['id'],
                    'translationCodeId' => $locale['id'],
                ];
            }

            $languages[$locale['code']] = $language;
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

        $locales = $connection->executeQuery(
            $sql,
            [\array_values($localeCodes)],
            [Connection::PARAM_STR_ARRAY]
        )->fetchAll();

        $enhancedLocales = [];
        foreach ($localeCodes as $name => $code) {
            foreach ($locales as $locale) {
                if ($code === $locale['code']) {
                    $locale['name'] = $name;
                    $enhancedLocales[$code] = $locale;
                }
            }
        }

        return $enhancedLocales;
    }

    public function bin2hex(array $data, array $keys)
    {
        return array_map(function ($entry) use ($keys) {
            foreach ($keys as $key) {
                $entry[$key] = Uuid::fromBytesToHex($entry[$key]);
            }
            return $entry;
        }, $data);
    }
}
