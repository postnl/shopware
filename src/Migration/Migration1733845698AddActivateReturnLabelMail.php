<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Migration;

use Doctrine\DBAL\Connection;
use PostNL\Shopware6\Component\Migration\MailMigration;
use PostNL\Shopware6\MailTemplate\ActivateShipmentAndReturnMail;

class Migration1733845698AddActivateReturnLabelMail extends MailMigration
{
    public function getCreationTimestamp(): int
    {
        return 1733845698;
    }

    public function update(Connection $connection): void
    {
        $emails = [
            'return-mail' => [
                new ActivateShipmentAndReturnMail\enGB(),
                new ActivateShipmentAndReturnMail\deDE(),
                new ActivateShipmentAndReturnMail\nlNL(),
            ],
        ];

        $mailTemplateId = $this->getMailTemplateTypeId($connection, 'postnl_activate_shipment_and_return_label_mail') ?? $this->createMailTemplateType(
            $connection,
            'postnl_activate_shipment_and_return_label_mail',
            [
                'en-GB' => 'PostNL Activate Shipment and Return Mail',
                'de-DE' => 'PostNL Aktivieren von Versand und Rücksendungsmail',
                'nl-NL' => 'PostNL Heen en terug label geactiveerd mail',
            ],
            [
                'order'        => 'order',
                'salesChannel' => 'sales_channel',
            ]
        );

        foreach ($emails as $mailTemplates) {
            $this->createMailTemplate($connection, $mailTemplateId, $mailTemplates);
        }
    }
}
