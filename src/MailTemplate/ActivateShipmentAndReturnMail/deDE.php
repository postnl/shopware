<?php

declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate\ActivateShipmentAndReturnMail;

use PostNL\Shopware6\MailTemplate\MailTemplateInterface;

class deDE implements MailTemplateInterface
{
    public static function getLocale(): string
    {
        return 'de-DE';
    }

    public static function getDescription(): string
    {
        return 'PostNL Aktivieren von Versand und Rücksendungsmail';
    }

    public static function getSubject(): string
    {
        return 'Ihre Rücksendung von {{ salesChannel.name }}';
    }

    public static function getContentPlain(): string
    {
        return <<<MAIL
Hallo {{ order.orderCustomer.firstName }} {{ order.orderCustomer.lastName }},

In dieser E-Mail finden Sie den Barcode, der für die Rücksendung Ihrer Bestellung erforderlich ist. Scannen Sie diesen
Barcode bei einer PostNL-Stelle ein, um das Rücksendeetikett auszudrucken. Bitte beachten Sie, dass Sie mindestens
10 Minuten nach Erhalt dieser E-Mail warten, bevor Sie den Barcode einscannen lassen.

Einen schönen Tag,

{{ salesChannel.name }}
MAIL;
    }

    public static function getContentHtml(): string
    {

        return <<<MAIL
<div style="font-family:arial; font-size:12px;">
<p>
Hallo {{ order.orderCustomer.firstName }} {{ order.orderCustomer.lastName }},
</p>
<p>
In dieser E-Mail finden Sie den Barcode, der für die Rücksendung Ihrer Bestellung erforderlich ist. Scannen Sie diesen
Barcode bei einer PostNL-Stelle ein, um das Rücksendeetikett auszudrucken. Bitte beachten Sie, dass Sie mindestens
10 Minuten nach Erhalt dieser E-Mail warten, bevor Sie den Barcode einscannen lassen.
</p>
<p>
Einen schönen Tag,
</p>
<p>
{{ salesChannel.name }}
</p>
</div>
MAIL;
    }

}