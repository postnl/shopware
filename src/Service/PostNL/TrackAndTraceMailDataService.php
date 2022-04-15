<?php

namespace PostNL\Shopware6\Service\PostNL;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Framework\Context;
use Symfony\Component\Mime\Email;

class TrackAndTraceMailDataService extends AbstractMailService
{

    /**
     * @var AbstractMailService
     */
    private AbstractMailService $mailService;

    public function __construct(AbstractMailService $mailService)
    {
        $this->mailService = $mailService;
    }

    public function getDecorated(): AbstractMailService
    {
        return $this->mailService;
    }

    public function send(array $data, Context $context, array $templateData = []): ?Email
    {
        /** @var OrderEntity $order */
        $order = $templateData['order'];
        if (isset($order->getCustomFields()['postnl']['barCode'])) {
            $barcode = $order->getCustomFields()['postnl']['barCode'];
            $zipCode = $order->getDeliveries()->first()->getShippingOrderAddress()->getZipcode();
            $countryCode = $order->getDeliveries()->first()->getShippingOrderAddress()->getCountry()->getIso();
            $templateData['postNL']['trackAndTraceLink'] = sprintf('http://postnl.nl/tracktrace/?B=%s&P=%s&D=%s&T=C', $barcode, $zipCode, $countryCode);
        }
        return $this->mailService->send($data, $context, $templateData);
    }
}
