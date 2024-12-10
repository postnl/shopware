<?php

declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate\ActivateShipmentAndReturnMail;

use PostNL\Shopware6\MailTemplate\MailTemplateInterface;

class nlNL implements MailTemplateInterface
{
    public static function getLocale(): string
    {
        return 'nl-NL';
    }

    public static function getDescription(): string
    {
        return 'PostNL Heen en terug label geactiveerd mail template';
    }

    public static function getSubject(): string
    {
        return 'Uw retourzending van {{ salesChannel.name }}';
    }

    public static function getContentPlain(): string
    {
        return <<<MAIL
Hi {{ order.orderCustomer.firstName }} {{ order.orderCustomer.lastName }},

In deze email vind je de barcode die nodig is om jouw bestelling te
retourneren. Scan deze barcode bij een PostNL punt om het retour-label te
printen. Let op, wacht minimaal 10 minuten na het ontvangen van deze mail
voordat je de barcode laat scannen.

Een fijne dag,

{{ salesChannel.name }}
MAIL;
    }

    public static function getContentHtml(): string
    {

        return <<<MAIL
<div style="font-family:arial; font-size:12px;">
<p>
Hi {{ order.orderCustomer.firstName }} {{ order.orderCustomer.lastName }},
</p>
<p>
In deze email vind je de barcode die nodig is om jouw bestelling te
retourneren. Scan deze barcode bij een PostNL punt om het retour-label te
printen. Let op, wacht minimaal 10 minuten na het ontvangen van deze mail
voordat je de barcode laat scannen.
</p>
<p>
Een fijne dag,
</p>
<p>
{{ salesChannel.name }}
</p>
</div>
MAIL;
    }

}