<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MailMigration;
use Shopware\Core\Framework\Log\Package;
use PostNL\Shopware6\MailTemplate\ReturnMailBe;

/**
 * @internal
 */
#[Package('core')]
class Migration1738685102AddReturnMailTemplateForBelgium extends MailMigration
{
    public function getCreationTimestamp(): int
    {
        return 1738685102;
    }

    public function update(Connection $connection): void
    {
        $emails = [
            'return-mail' => [
                new ReturnMailBe\enGB(),
                new ReturnMailBe\deDE(),
                new ReturnMailBE\nlNL(),
            ]
        ];

        $mailTemplateId = $this->createMailTemplateType(
            $connection,
            'postnl_return_mail_be',
            [
                'en-GB' => 'PostNL Return Mail (BE)',
                'de-DE' => 'PostNL Rücksendung (BE)',
                'nl-NL' => 'PostNL Retour Mail (BE)',
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
}
