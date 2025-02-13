<?php

declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate\ReturnMail;

use PostNL\Shopware6\MailTemplate\MailTemplateInterface;

class enGB implements MailTemplateInterface
{
    public static function getLocale(): string
    {
        return 'en-GB';
    }

    public static function getDescription(): string
    {
        return 'PostNL return mail template';
    }

    public static function getSubject(): string
    {
        return 'Your return shipment from {{ salesChannel.name }}';
    }

    public static function getContentPlain(): string
    {
        return <<<MAIL
Hi {{ order.orderCustomer.firstName }} {{ order.orderCustomer.lastName }},

In this email you will find the barcode required to return your order. Scan this
barcode at a PostNL point to print the return label. Please note, wait at least
10 minutes after receiving this email before having the barcode scanned.

Have a good day,

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
In this email you will find the barcode required to return your order. Scan this
barcode at a PostNL point to print the return label. Please note, wait at least
10 minutes after receiving this email before having the barcode scanned.
</p>
<p>
Have a good day,
</p>
<p>
{{ salesChannel.name }}
</p>
</div>
MAIL;
    }

}