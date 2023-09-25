<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PostNL\Shopware6\Migration\MailTemplates\MailTemplate_deDE;
use PostNL\Shopware6\Migration\MailTemplates\MailTemplate_enGB;
use PostNL\Shopware6\Migration\MailTemplates\MailTemplate_nlNL;
use PostNL\Shopware6\Migration\MailTemplates\MailTemplateInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1649846266MailTemplateCreation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1649846266;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $mailTemplates = [new MailTemplate_enGB(),new MailTemplate_deDE(),new MailTemplate_nlNL()];

        $mailTemplateTypeShippedId = $this->getMailTemplateTypeId($connection, 'order_delivery.state.shipped');
        $mailTemplateTypeShippedPartiallyId = $this->getMailTemplateTypeId($connection, 'order_delivery.state.shipped_partially');

        //Add the new template
        foreach ($mailTemplates as $mailTemplate) {
            $this->createMailTemplate($connection, $mailTemplateTypeShippedId, $mailTemplate);
            $this->createMailTemplate($connection, $mailTemplateTypeShippedPartiallyId, $mailTemplate);
        }

    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    /**
     * @param Connection $connection
     * @param string $technicalName
     * @return string
     * @throws Exception
     */
    private function getMailTemplateTypeId(Connection $connection, string $technicalName): string
    {
        $sql = sprintf('SELECT id
            FROM mail_template_type
            WHERE technical_name = "%s";',$technicalName);


        return Uuid::fromBytesToHex($connection->fetchOne($sql));
    }

    /**
     * @param Connection $connection
     * @param string $locale
     * @return string|null
     * @throws Exception
     */
    private function getLanguageIdByLocale(Connection $connection, string $locale): ?string
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
     * @param Connection $connection
     * @param string $mailTemplateTypeId
     * @param MailTemplateInterface $mailTemplate
     * @return void
     * @throws Exception
     */
    private function createMailTemplate(Connection $connection, string $mailTemplateTypeId, MailTemplateInterface $mailTemplate)
    {
        $mailTemplateId = Uuid::randomHex();

        $connection->insert('mail_template', [
            'id' => Uuid::fromHexToBytes($mailTemplateId),
            'mail_template_type_id' => Uuid::fromHexToBytes($mailTemplateTypeId),
            'system_default' => 0,
            'created_at' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        $this->insertMailTemplateTranslation($connection, $mailTemplateId, $mailTemplate);
    }

    /**
     * @param Connection $connection
     * @param string $mailTemplateId
     * @param MailTemplateInterface $mailTemplate
     * @return void
     * @throws Exception
     */
    private function insertMailTemplateTranslation(
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
            'created_at' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }
}
