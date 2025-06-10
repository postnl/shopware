const components = import.meta.glob('./../app/component/**/index.js')

Object
    .entries(components)
    .forEach(([path, importFn]) => Shopware.Component.register(path.split('/').slice(-2).shift(), importFn))
