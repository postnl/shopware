const components = import.meta.glob([
    './../component/**/index.{j,t}s',
    './../../module/*/*/**/index.{j,t}s'
])
// console.log(components)
import config from './component.json'

Object
    .entries(components)
    .forEach(([path, importFn]) => {
        const componentName = path.split('/').slice(-2).shift()

        if (config?.extend?.[componentName]) {
            Shopware.Component.extend(componentName, config?.extend?.[componentName], importFn)
            return
        }

        if (config?.override && componentName in config?.override) {
            Shopware.Component.override(componentName, importFn)
            return
        }

        // console.log(`Registering component ${componentName}`)
        Shopware.Component.register(componentName, importFn)
    })
