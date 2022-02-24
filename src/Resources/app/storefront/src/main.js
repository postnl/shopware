import PluginManager from 'src/plugin-system/plugin.manager';

import PostnlPickupPointsPlugin from './plugin/postnl-pickup-points.plugin';

PluginManager.register('PostnlPickupPoints', PostnlPickupPointsPlugin, '[data-postnl-pickup-points]');
