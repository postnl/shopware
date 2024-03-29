<?php

namespace PostNL\Shopware6\Service\Shopware;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\SalesChannel\CartService as ShopwareCartService;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class CartService
{
    public const EXTENSION = 'postnl-data';
    public const ORIGINAL_DATA = 'postnl-order-data';

    protected ShopwareCartService $cartService;

    public function __construct(ShopwareCartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function hasData(SalesChannelContext $context, ?string $key = null): bool
    {
        $cart = $this->cartService->getCart($context->getToken(), $context);

        $hasData = $cart->hasExtensionOfType(self::EXTENSION, ArrayStruct::class);

        if(empty($key)) {
            return $hasData;
        }

        if(!$hasData) {
            return false;
        }

        /** @var ArrayStruct $data */
        $data = $cart->getExtensionOfType(self::EXTENSION, ArrayStruct::class);

        return $data->has($key);
    }

    public function addData(array $data, SalesChannelContext $context): Cart
    {
        $cart = $this->cartService->getCart($context->getToken(), $context);

        if(!$cart->hasExtensionOfType(self::EXTENSION, ArrayStruct::class)) {
            $cart->addExtension(self::EXTENSION, new ArrayStruct());
        }

        /** @var ArrayStruct $postnlData */
        $postnlData = $cart->getExtensionOfType(self::EXTENSION, ArrayStruct::class);

        foreach($data as $key => $value) {
            $postnlData->set($key, $value);
        }

        // Will save the cart to the database
        return $this->cartService->recalculate($cart, $context);
    }

    public function getData(SalesChannelContext $context): array
    {
        $cart = $this->cartService->getCart($context->getToken(), $context);

        if(!$cart->hasExtensionOfType(self::EXTENSION, ArrayStruct::class)) {
            return [];
        }

        /** @var ArrayStruct $postnlData */
        $postnlData = $cart->getExtensionOfType(self::EXTENSION, ArrayStruct::class);

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
