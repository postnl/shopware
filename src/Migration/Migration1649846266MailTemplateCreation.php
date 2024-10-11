<?php
declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use PostNL\Shopware6\Component\Migration\MailMigration;
use PostNL\Shopware6\MailTemplate\TrackAndTraceMail;

class Migration1649846266MailTemplateCreation extends MailMigration
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
        $emails = [
            'track-and-trace' => [
                new TrackAndTraceMail\enGB(),
                new TrackAndTraceMail\deDE(),
                new TrackAndTraceMail\nlNL(),
            ]
        ];

        $mailTemplateTypeShippedId = $this->getMailTemplateTypeId($connection, 'order_delivery.state.shipped');
        $mailTemplateTypeShippedPartiallyId = $this->getMailTemplateTypeId($connection, 'order_delivery.state.shipped_partially');

        //Add the new template
        foreach ($emails as $mailTemplates) {
            $this->createMailTemplate($connection, $mailTemplateTypeShippedId, $mailTemplates);
            $this->createMailTemplate($connection, $mailTemplateTypeShippedPartiallyId, $mailTemplates);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
