<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MailMigration;
use PostNL\Shopware6\MailTemplate\ReturnMail;

class Migration1728648178AddReturnMailTemplate extends MailMigration
{
    public function getCreationTimestamp(): int
    {
        return 1728648178;
    }

    public function update(Connection $connection): void
    {
        $emails = [
            'return-mail' => [
                new ReturnMail\enGB(),
                new ReturnMail\deDE(),
                new ReturnMail\nlNL(),
            ]
        ];

        $mailTemplateId = $this->getMailTemplateTypeId($connection, 'postnl_return_mail') ?? $this->createMailTemplateType(
            $connection,
            'postnl_return_mail',
            [
                'en-GB' => 'PostNL Return Mail',
                'de-DE' => 'PostNL Rücksendung',
                'nl-NL' => 'PostNL Retour Mail',
            ],
            [
                'order' => 'order',
                'salesChannel' => 'sales_channel'
            ]
        );

        foreach ($emails as $mailTemplates) {
            $this->createMailTemplate($connection, $mailTemplateId, $mailTemplates);
        }
    }

    public function updateDestructive(Connection $connection): void
    {

    }
}
