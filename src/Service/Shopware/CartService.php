<?php

namespace PostNL\Shipments\Service\Shopware;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService as ShopwareCartService;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class CartService
{
    protected $cartService;

    public function __construct(ShopwareCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function addData(array $data, SalesChannelContext $context): Cart
    {
        $cart = $this->cartService->getCart($context->getToken(), $context);

        if(!$cart->hasExtensionOfType('postnl-data', ArrayStruct::class)) {
            $cart->addExtension('postnl-data', new ArrayStruct());
        }

        /** @var ArrayStruct $postnlData */
        $postnlData = $cart->getExtensionOfType('postnl-data', ArrayStruct::class);

        foreach($data as $key => $value) {
            $postnlData->set($key, $value);
        }

        // Will save the cart to the database
        return $this->cartService->recalculate($cart, $context);
    }

    public function getData(SalesChannelContext $context): array
    {
        $cart = $this->cartService->getCart($context->getToken(), $context);

        if(!$cart->hasExtensionOfType('postnl-data', ArrayStruct::class)) {
            return [];
        }

        /** @var ArrayStruct $postnlData */
        $postnlData = $cart->getExtensionOfType('postnl-data', ArrayStruct::class);

        return $postnlData->all();
    }

    public function getByKey(string $key, SalesChannelContext $context)
    {
        $data = $this->getData($context);

        if(!array_key_exists($key, $data)) {
            return null;
        }

        return $data[$key];
    }
}
