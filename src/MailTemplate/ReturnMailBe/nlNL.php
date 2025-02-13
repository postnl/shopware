<?php

declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate\ReturnMailBe;

use PostNL\Shopware6\MailTemplate\MailTemplateInterface;

class nlNL implements MailTemplateInterface
{
    public static function getLocale(): string
    {
        return 'nl-NL';
    }

    public static function getDescription(): string
    {
        return 'PostNL retour mail template';
    }

    public static function getSubject(): string
    {
        return 'Uw retourzending van {{ salesChannel.name }}';
    }

    public static function getContentPlain(): string
    {
        return <<<MAIL
Hi {{ order.orderCustomer.firstName }} {{ order.orderCustomer.lastName }},

Jouw retouraanvraag is verwerkt. In deze e-mail vind je het verzendlabel om het pakket te retourneren. 
Plak het label op het pakket en breng het naar een PostNL punt in de buurt.

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
Jouw retouraanvraag is verwerkt. In deze e-mail vind je het verzendlabel om het pakket te retourneren. 
Plak het label op het pakket en breng het naar een PostNL punt in de buurt.
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