import PluginManager from 'src/plugin-system/plugin.manager';

import PostnlPickupPointsPlugin from './plugin/postnl-pickup-points.plugin';
import PostnlPostalCodeCheckPlugin from "./plugin/postnl-postal-code-check.plugin";
import PostnlShippingDeliveryDatePlugin from "./plugin/postnl-shipping-delivery-date.plugin";

PluginManager.register('PostnlPickupPoints', PostnlPickupPointsPlugin, '[data-postnl-pickup-points]');
PluginManager.register('PostnlPostalCodeCheck', PostnlPostalCodeCheckPlugin, '[data-postnl-postal-code-check]');
PluginManager.register('PostnlShippingDeliveryDate', PostnlShippingDeliveryDatePlugin, '[data-postnl-shipping-delivery-date]');
