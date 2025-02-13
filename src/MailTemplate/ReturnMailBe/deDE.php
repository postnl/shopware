<?php

declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate\ReturnMailBe;

use PostNL\Shopware6\MailTemplate\MailTemplateInterface;

class deDE implements MailTemplateInterface
{
    public static function getLocale(): string
    {
        return 'de-DE';
    }

    public static function getDescription(): string
    {
        return 'PostNL-Rücksendevorlage';
    }

    public static function getSubject(): string
    {
        return 'Ihre Rücksendung von {{ salesChannel.name }}';
    }

    public static function getContentPlain(): string
    {
        return <<<MAIL
Hallo {{ order.orderCustomer.firstName }} {{ order.orderCustomer.lastName }},

Ihr Rücksendeantrag wurde bearbeitet. In dieser E-Mail finden Sie den Versandaufkleber für die Rücksendung Ihrer Bestellung. 
Bringen Sie den Aufkleber am Paket an und bringen Sie Ihre Sendung zu einer PostNL-Stelle.

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
Ihr Rücksendeantrag wurde bearbeitet. In dieser E-Mail finden Sie den Versandaufkleber für die Rücksendung Ihrer Bestellung. 
Bringen Sie den Aufkleber am Paket an und bringen Sie Ihre Sendung zu einer PostNL-Stelle.
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