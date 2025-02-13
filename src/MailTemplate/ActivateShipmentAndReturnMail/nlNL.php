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

Jouw retouraanvraag is verwerkt. Je kunt nu het pakket retourneren. Het verzendlabel dat al op de verpakking zat, werkt ook als retourlabel. Scan deze barcode bij een PostNL punt om jouw zending te retourneren.

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
Jouw retouraanvraag is verwerkt. Je kunt nu het pakket retourneren. Het verzendlabel dat al op de verpakking zat, werkt ook als retourlabel. Scan deze barcode bij een PostNL punt om jouw zending te retourneren.
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