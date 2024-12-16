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

Ihr Rücksendeantrag wurde bearbeitet. Sie können das Paket nun zurückschicken.

Der Versandaufkleber, der sich bereits auf dem Paket befindet, funktioniert auch als Rücksendeaufkleber. Scannen Sie den Barcode an einer PostNL-Stelle ein, um Ihre Sendung zurückzusenden.

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
Ihr Rücksendeantrag wurde bearbeitet. Sie können das Paket nun zurückschicken.

Der Versandaufkleber, der sich bereits auf dem Paket befindet, funktioniert auch als Rücksendeaufkleber. Scannen Sie den Barcode an einer PostNL-Stelle ein, um Ihre Sendung zurückzusenden.
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