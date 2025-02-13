<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Component\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PostNL\Shopware6\MailTemplate\MailTemplateInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

abstract class MailMigration extends MigrationStep
{
    protected function createMailTemplateType(
        Connection $connection,
        string $technicalName,
        array $nameTranslations,
        array $availableEntities
    ): string
    {
        $id = Uuid::randomHex();

        $connection->insert(
            'mail_template_type',
            [
                'id'                 => Uuid::fromHexToBytes($id),
                'technical_name'     => $technicalName,
                'available_entities' => json_encode($availableEntities),
                'created_at'         => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                'template_data'      => null,
            ]
        );

        foreach($nameTranslations as $locale => $name) {
            $languageId = $this->getLanguageIdByLocale($connection, $locale);

            $connection->insert(
                'mail_template_type_translation',
                [
                    'mail_template_type_id' => Uuid::fromHexToBytes($id),
                    'language_id'           => $languageId,
                    'name'                  => $name,
                    'created_at'            => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                ]
            );
        }

        return $id;
    }

    /**
     * @throws Exception
     */
    protected function getMailTemplateTypeId(Connection $connection, string $technicalName): string
    {
        $sql = sprintf('SELECT id
            FROM mail_template_type
            WHERE technical_name = "%s";',$technicalName);


        return Uuid::fromBytesToHex($connection->fetchOne($sql));
    }

    /**
     * @throws Exception
     */
    protected function getLanguageIdByLocale(Connection $connection, string $locale): ?string
    {
        $sql = <<<SQL
SELECT `language`.`id`
FROM `language`
INNER JOIN `locale` ON `locale`.`id` = `language`.`locale_id`
WHERE `locale`.`code` = :code
SQL;

        $languageId = $connection->executeQuery($sql, ['code' => $locale])->fetchOne();
        if (!$languageId && $locale !== 'en-GB') {
            return null;
        }

        if (!$languageId) {
            return Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        }

        return $languageId;
    }

    /**
     * @param MailTemplateInterface[] $mailTemplates
     * @throws Exception
     */
    protected function createMailTemplate(Connection $connection, string $mailTemplateTypeId, array $mailTemplates)
    {
        $mailTemplateId = Uuid::randomHex();

        $connection->insert('mail_template', [
            'id' => Uuid::fromHexToBytes($mailTemplateId),
            'mail_template_type_id' => Uuid::fromHexToBytes($mailTemplateTypeId),
            'system_default' => 0,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        foreach($mailTemplates as $mailTemplate) {
            $this->insertMailTemplateTranslation($connection, $mailTemplateId, $mailTemplate);
        }
    }

    /**
     * @throws Exception
     */
    protected function insertMailTemplateTranslation(
        Connection            $connection,
        string                $mailTemplateId,
        MailTemplateInterface $mailTemplate)
    {
        $langId = $this->getLanguageIdByLocale($connection, $mailTemplate->getLocale());

        $connection->insert('mail_template_translation', [
            'mail_template_id' => Uuid::fromHexToBytes($mailTemplateId),
            'language_id' => $langId,
            'sender_name' => '{{ salesChannel.name }}',
            'subject' => $mailTemplate->getSubject(),
            'description' => $mailTemplate->getDescription(),
            'content_html' => $mailTemplate->getContentHtml(),
            'content_plain' => $mailTemplate->getContentPlain(),
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }
}