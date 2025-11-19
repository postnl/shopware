<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shopware6\Struct\Attribute\OrderAttributeStruct;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Framework\Context;
use Symfony\Component\Mime\Email;

class TrackAndTraceMailDataService extends AbstractMailService
{
    public function __construct(
        protected AbstractMailService $mailService,
        protected AttributeFactory    $attributeFactory,
        protected OrderDataExtractor  $orderDataExtractor,
    ) {}

    public function getDecorated(): AbstractMailService
    {
        return $this->mailService;
    }

    public function send(array $data, Context $context, array $templateData = []): ?Email
    {
        if (!array_key_exists('order', $templateData)) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        $order = $templateData['order'];

        if (!$order instanceof OrderEntity) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        /** @var OrderAttributeStruct $orderAttributes */
        $orderAttributes = $this->attributeFactory->createFromEntity($order, $context);
        $barcode = $orderAttributes->getBarCode();

        // If the email template uses the track and trace variable, then Shopware might break when it cannot replace
        // that variable. So always set it to an empty string first and set it to the correct link if we have all data.
        $templateData['postNL']['trackAndTraceLink'] = '';

        if (is_null($barcode)) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        $languageCode = match ($order->getLanguage()?->getLocale()?->getCode()) {
            null => null,
            'nl-NL', 'nl-BE', 'nl-AW', 'nl-BQ', 'nl-CW', 'nl-SR', 'nl-SX' => 'nl',
            default => 'en',
        };

        if (is_null($languageCode)) {
            try {
                $customer = $this->orderDataExtractor->extractCustomer($order);
                $languageCode = $customer->getCustomer()?->getLanguage()?->getLocale()?->getCode();
            }
            catch (\Throwable $e) {
                $languageCode = null;
            }
        }

        try {
            $shippingAddress = $this->orderDataExtractor->extractDeliveryAddress($order);
            $shippingCountry = $this->orderDataExtractor->extractDeliveryCountry($order);

            $templateData['postNL']['trackAndTraceLink'] = sprintf(
                'https://jouw.postnl.nl/track-and-trace/%s-%s-%s?language=%s',
                $barcode,
                $shippingAddress->getZipcode(),
                $shippingCountry->getIso(),
                $languageCode ?? $shippingCountry->getIso() === 'NL' ? 'nl' : 'en',
            );
        }
        catch (\Throwable $e) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        return $this->getDecorated()->send($data, $context, $templateData);
    }
}
