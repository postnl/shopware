<?php declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate\TrackAndTraceMail;

use PostNL\Shopware6\MailTemplate\MailTemplateInterface;

class nlNL implements MailTemplateInterface
{

    public static function getLocale(): string
    {
        return 'nl-NL';
    }

    public static function getDescription(): string
    {
        return 'PostNL Track & Trace e-mail sjabloon';
    }

    public static function getSubject(): string
    {
        return 'Je bestelling met bestelnummer {{order.orderNumber}} is onderweg.';
    }


    public static function getContentPlain(): string
    {
        return <<<MAIL
Goed nieuws! Je bestelling met bestelnummer {{order.orderNumber}} is klaargemaakt en komt zo snel mogelijk met PostNL naar je toe. Binnen enkele uren kun je je bestelling volgen via onderstaande link:

{{postNL.trackAndTraceLink}}

Houd de Track & Trace link in de gaten voor de meest actuele status van jouw pakket.
MAIL;
    }

    public static function getContentHtml(): string
    {
        return <<<MAIL
<div style="font-family:arial; font-size:12px;">
<p>
Goed nieuws! Je bestelling met bestelnummer {{order.orderNumber}} is klaargemaakt en komt zo snel mogelijk met PostNL naar je toe. Binnen enkele uren kun je je bestelling volgen via onderstaande link:
</p>
<p>
<a href="{{postNL.trackAndTraceLink}}">{{postNL.trackAndTraceLink}}</a>
</p>
<p>
Houd de Track & Trace link in de gaten voor de meest actuele status van jouw pakket.
</p>
</div>
MAIL;
    }


}
