const bootPromise = Shopware.Plugin.addBootPromise()

void import('./core/postnl')
    .then(module => module.default)
    .then(({ PostNLInstance }) => window.PostNLShopware = PostNLInstance)
    .then(() => import('./app/init'))
    .finally(() => bootPromise())