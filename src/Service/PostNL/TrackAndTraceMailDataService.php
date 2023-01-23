<?php

namespace PostNL\Shopware6\Service\PostNL;

use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderAddressDataExtractor;
use PostNL\Shopware6\Service\Shopware\DataExtractor\OrderDataExtractor;
use PostNL\Shopware6\Struct\Attribute\OrderAttributeStruct;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Framework\Context;
use Symfony\Component\Mime\Email;

class TrackAndTraceMailDataService extends AbstractMailService
{

    /**
     * @var AbstractMailService
     */
    protected AbstractMailService $mailService;

    /**
     * @var AttributeFactory
     */
    protected AttributeFactory $attributeFactory;

    /**
     * @var OrderDataExtractor
     */
    protected OrderDataExtractor $orderDataExtractor;

    /**
     * @var OrderAddressDataExtractor
     */
    protected OrderAddressDataExtractor $orderAddressDataExtractor;

    public function __construct(
        AbstractMailService $mailService,
        AttributeFactory $attributeFactory,
        OrderDataExtractor $orderDataExtractor,
        OrderAddressDataExtractor $orderAddressDataExtractor
    )
    {
        $this->mailService = $mailService;
        $this->attributeFactory = $attributeFactory;
        $this->orderDataExtractor = $orderDataExtractor;
        $this->orderAddressDataExtractor = $orderAddressDataExtractor;
    }

    public function getDecorated(): AbstractMailService
    {
        return $this->mailService;
    }

    public function send(array $data, Context $context, array $templateData = []): ?Email
    {
        if(!array_key_exists('order', $templateData)) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        $order = $templateData['order'];

        if(!$order instanceof OrderEntity) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        /** @var OrderAttributeStruct $orderAttributes */
        $orderAttributes = $this->attributeFactory->createFromEntity($order, $context);
        $barcode = $orderAttributes->getBarCode();

        if (is_null($barcode)) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        try {
            $shippingAddress = $this->orderDataExtractor->extractDeliveryAddress($order);
            $shippingCountry = $this->orderDataExtractor->extractDeliveryCountry($order);

            $templateData['postNL']['trackAndTraceLink'] = sprintf(
                'http://postnl.nl/tracktrace/?B=%s&P=%s&D=%s&T=C',
                $barcode,
                $shippingAddress->getZipcode(),
                $shippingCountry->getIso()
            );
        } catch(\Throwable $e) {
            return $this->getDecorated()->send($data, $context, $templateData);
        }

        return $this->mailService->send($data, $context, $templateData);
    }
}
