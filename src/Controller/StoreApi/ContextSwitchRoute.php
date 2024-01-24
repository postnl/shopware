<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Controller\StoreApi;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Struct\TimeframeStruct;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\ContextTokenResponse;
use Shopware\Core\System\SalesChannel\SalesChannel\AbstractContextSwitchRoute;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class ContextSwitchRoute extends AbstractContextSwitchRoute
{
    private AbstractContextSwitchRoute $decorated;

    private CartService $cartService;

    public function __construct(
        AbstractContextSwitchRoute $decorated,
        CartService                $cartService
    )
    {
        $this->decorated = $decorated;
        $this->cartService = $cartService;
    }

    public function getDecorated(): AbstractContextSwitchRoute
    {
        return $this->decorated;
    }

    public function switchContext(RequestDataBag $data, SalesChannelContext $context): ContextTokenResponse
    {
        $parameters = $data->only(
            'postnl-shipping-delivery-time',
            'postnl-pickup-location'
        );

        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'postnl-shipping-delivery-time':
                    $this->handleDeliveryTime(TimeframeStruct::createFromJson($value), $context);
                    break;
                case 'postnl-pickup-location':
                    $this->handlePickupLocationCode($value, $context);
                    break;
            }
        }

        return $this->getDecorated()->switchContext($data, $context);
    }

    private function handleDeliveryTime(TimeframeStruct $timeframe, SalesChannelContext $context): void
    {
        $this->cartService->addData(
            [
                Defaults::CUSTOM_FIELDS_TIMEFRAME_KEY     => $timeframe,
                Defaults::CUSTOM_FIELDS_DELIVERY_DATE_KEY => $timeframe->getFrom(),
            ],
            $context
        );
    }

    private function handlePickupLocationCode(string $locationCode, SalesChannelContext $context): void
    {
        $this->cartService->addData(
            [
                'pickupPointLocationCode' => (int)$locationCode
            ],
            $context
        );
    }
}