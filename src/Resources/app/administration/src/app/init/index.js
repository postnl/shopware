import './api-service.init'
import './store.init'

import './decorator.init'
import './../mixin'

import './component.init'
import './module.init'

const resolve = Shopware.Plugin.addBootPromise()
Shopware.Store.get('postnlCountryCache').load().then(() => resolve())