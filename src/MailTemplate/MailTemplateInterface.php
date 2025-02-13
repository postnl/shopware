<?php declare(strict_types=1);

namespace PostNL\Shopware6\MailTemplate;

interface MailTemplateInterface
{
    public static function getLocale(): string;

    public static function getDescription(): string;

    public static function getSubject(): string;

    public static function getContentPlain(): string;

    public static function getContentHtml(): string;
}
