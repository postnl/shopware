import PluginManager from 'src/plugin-system/plugin.manager';

import PostnlPostalCodeCheckPlugin from "./plugin/postnl-postal-code-check.plugin";

PluginManager.register('PostnlPostalCodeCheck', PostnlPostalCodeCheckPlugin, '[data-postnl-postal-code-check]');
