<?php

declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate\ReturnMailBe;

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

Your return request has been processed. In this email you will find the shipping label to return your order. 
Attach the label to the parcel and bring your shipment to a PostNL point.

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
Your return request has been processed. In this email you will find the shipping label to return your order. 
Attach the label to the parcel and bring your shipment to a PostNL point.
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