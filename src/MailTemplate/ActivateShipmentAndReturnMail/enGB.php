<?php

declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate\ActivateShipmentAndReturnMail;

use PostNL\Shopware6\MailTemplate\MailTemplateInterface;

class enGB implements MailTemplateInterface
{
    public static function getLocale(): string
    {
        return 'en-GB';
    }

    public static function getDescription(): string
    {
        return 'PostNL Activate Shipment and Return Mail template';
    }

    public static function getSubject(): string
    {
        return 'Your return shipment from {{ salesChannel.name }}';
    }

    public static function getContentPlain(): string
    {
        return <<<MAIL
Hi {{ order.orderCustomer.firstName }} {{ order.orderCustomer.lastName }},

Your return request has been processed. You can now return the parcel.

The shipping label that is already on the parcel also works as a return label. Scan the barcode at a PostNL point to return your shipment.

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
Your return request has been processed. You can now return the parcel.

The shipping label that is already on the parcel also works as a return label. Scan the barcode at a PostNL point to return your shipment.
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