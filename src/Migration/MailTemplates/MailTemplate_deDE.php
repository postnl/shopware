<?php declare(strict_types=1);

namespace PostNL\Shopware6\Migration\MailTemplates;

class MailTemplate_deDE implements MailTemplateInterface
{

    public static function getLocale(): string
    {
        return 'de-DE';
    }

    public static function getDescription(): string
    {
        return 'PostNL Track & Trace E-Mail-Vorlage';
    }

    public static function getSubject(): string
    {
        return 'Ihre Bestellung mit der Bestellnummer {{order.orderNumber}} ist unterwegs.';
    }


    public static function getContentPlain(): string
    {
        return <<<MAIL
Gute Nachrichten! Ihre Bestellung mit der Bestellnummer {{order.orderNumber}} wurde vorbereitet und wird Ihnen so schnell wie möglich mit PostNL zugestellt. Innerhalb weniger Stunden können Sie Ihre Bestellung über den folgenden Link verfolgen:

{{postNL.trackAndTraceLink}}

Behalten Sie den Track & Trace-Link im Auge, um den aktuellsten Status Ihres Pakets zu erhalten.
MAIL;
    }

    public static function getContentHtml(): string
    {
        return <<<MAIL
<div style="font-family:arial; font-size:12px;">
    <p>
Gute Nachrichten! Ihre Bestellung mit der Bestellnummer {{order.orderNumber}} wurde vorbereitet und wird Ihnen so schnell wie möglich mit PostNL zugestellt. Innerhalb weniger Stunden können Sie Ihre Bestellung über den folgenden Link verfolgen:
<br>
<a href="{{postNL.trackAndTraceLink}}">{{postNL.trackAndTraceLink}}</a>
<br>
Behalten Sie den Track & Trace-Link im Auge, um den aktuellsten Status Ihres Pakets zu erhalten.
</p>
</div>
MAIL;
    }


}
