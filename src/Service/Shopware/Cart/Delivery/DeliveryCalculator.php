<?php

declare(strict_types=1);

namespace PostNL\Shopware6\Service\Shopware\Cart\Delivery;

use PostNL\Shopware6\Defaults;
use PostNL\Shopware6\Service\Attribute\Factory\AttributeFactory;
use PostNL\Shopware6\Service\Shopware\CartService;
use PostNL\Shopware6\Service\Shopware\ConfigService;
use PostNL\Shopware6\Struct\Attribute\ShippingMethodAttributeStruct;
use PostNL\Shopware6\Struct\Config\ConfigStruct;
use PostNL\Shopware6\Struct\TimeframeStruct;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\Delivery\DeliveryCalculator as ShopwareDeliveryCalculator;
use Shopware\Core\Checkout\Cart\Delivery\Struct\Delivery;
use Shopware\Core\Checkout\Cart\Delivery\Struct\DeliveryCollection;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\PercentageTaxRuleBuilder;
use Shopware\Core\Defaults as ShopwareDefaults;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class DeliveryCalculator extends ShopwareDeliveryCalculator
{
    private QuantityPriceCalculator $priceCalculator;

    private AttributeFactory $attributeFactory;

    private ConfigService $configService;

    /**
     * @param QuantityPriceCalculator  $priceCalculator
     * @param PercentageTaxRuleBuilder $percentageTaxRuleBuilder
     * @param AttributeFactory         $attributeFactory
     * @param ConfigService            $configService
     */
    public function __construct(
        QuantityPriceCalculator $priceCalculator,
        PercentageTaxRuleBuilder $percentageTaxRuleBuilder,
        AttributeFactory $attributeFactory,
        ConfigService $configService
    )
    {
        parent::__construct($priceCalculator, $percentageTaxRuleBuilder);

        $this->priceCalculator = $priceCalculator;
        $this->attributeFactory = $attributeFactory;
        $this->configService = $configService;
    }

    /**
     * @param CartDataCollection  $data
     * @param Cart                $cart
     * @param DeliveryCollection  $deliveries
     * @param SalesChannelContext $context
     * @return void
     */
    public function calculate(
        CartDataCollection $data,
        Cart $cart,
        DeliveryCollection $deliveries,
        SalesChannelContext $context
    ): void
    {
        parent::calculate($data, $cart, $deliveries, $context);

        if(!$cart->hasExtensionOfType(CartService::EXTENSION, ArrayStruct::class)) {
            return;
        }

        /** @var ArrayStruct $postNlData */
        $postNlData = $cart->getExtensionOfType(CartService::EXTENSION, ArrayStruct::class);

        foreach($deliveries as $delivery) {
            $this->calculateSurcharge($postNlData, $delivery, $context);
        }
    }

    private function calculateSurcharge(
        ArrayStruct $data,
        Delivery $delivery,
        SalesChannelContext $context
    ): void
    {
        /** @var ShippingMethodAttributeStruct $shippingMethodAttributes */
        $shippingMethodAttributes = $this->attributeFactory->createFromEntity($delivery->getShippingMethod(), $context->getContext());

        if(empty($shippingMethodAttributes->getDeliveryType())) {
            return;
        }

        $config = $this->configService->getConfiguration($context->getSalesChannelId(), $context->getContext());

        switch($shippingMethodAttributes->getDeliveryType()) {
            case 'shipment':
                $this->calculateShipmentSurcharge($data, $delivery, $config, $context);
                break;
        }
    }

    private function calculateShipmentSurcharge(
        ArrayStruct $data,
        Delivery $delivery,
        ConfigStruct $config,
        SalesChannelContext $context
    ): void
    {
        if(!$data->has(Defaults::CUSTOM_FIELDS_TIMEFRAME_KEY)) {
            return;
        }

        /** @var TimeframeStruct $timeframe */
        $timeframe = $data->get(Defaults::CUSTOM_FIELDS_TIMEFRAME_KEY);

        $surcharge = 0;

        foreach($timeframe->getOptions() as $option) {
            switch(strtolower($option)) {
                case 'evening':
                    if($config->getEveningDelivery()) {
                        $surcharge += $config->getEveningSurcharge();
                    }
                    break;
            }
        }

        if($context->getCurrency()->getId() === ShopwareDefaults::CURRENCY) {
            $surcharge *= $context->getContext()->getCurrencyFactor();
        }

        $shippingCosts = $delivery->getShippingCosts();

        $definition = new QuantityPriceDefinition(
            $shippingCosts->getUnitPrice() + $surcharge,
            $shippingCosts->getTaxRules(),
            $shippingCosts->getQuantity()
        );

        $calculatedPrice = $this->priceCalculator->calculate($definition, $context);

        $delivery->setShippingCosts($calculatedPrice);
    }
}
