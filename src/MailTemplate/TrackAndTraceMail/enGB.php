<?php declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate\TrackAndTraceMail;

use PostNL\Shopware6\MailTemplate\MailTemplateInterface;

class enGB implements MailTemplateInterface
{

    public static function getLocale(): string
    {
        return 'en-GB';
    }

    public static function getDescription(): string
    {
        return 'PostNL Track & Trace mail template';
    }

    public static function getSubject(): string
    {
        return 'Your order with order number {{order.orderNumber}} is on its way.';
    }


    public static function getContentPlain(): string
    {
        return <<<MAIL
Good news! Your order with order number {{order.orderNumber}} has been prepared and will come to you as soon as possible with PostNL. Within a few hours you can track your order via the link below:

{{postNL.trackAndTraceLink}}

Keep an eye on the Track & Trace link for the most current status of your package.
MAIL;
    }

    public static function getContentHtml(): string
    {
        return <<<MAIL
<div style="font-family:arial; font-size:12px;">
<p>
Good news! Your order with order number {{order.orderNumber}} has been prepared and will come to you as soon as possible with PostNL. Within a few hours you can track your order via the link below:
</p>
<p>
<a href="{{postNL.trackAndTraceLink}}">{{postNL.trackAndTraceLink}}</a>
</p>
<p>
Keep an eye on the Track & Trace link for the most current status of your package.
</p>
</div>
MAIL;
    }


}
