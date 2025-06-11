const components = import.meta.glob('./../component/**/index.js')
import config from './../component/config.json'

Object
    .entries(components)
    .forEach(([path, importFn]) => {
        const componentName = path.split('/').slice(-2).shift()
        const allowedTypes = ['extend', 'override']

        if (config?.[componentName]) {
            if (!'type' in config[componentName] || !allowedTypes.includes(config[componentName].type)) {
                throw `Missing "type" field in ${ componentName } configuration. Must be one of ${ allowedTypes.join(', 0') }`
            }

            if (config[componentName].type === 'extend') {
                if (!'component' in config[componentName]) {
                    throw `Missing "component" field in ${ componentName } configuration.`
                }

                Shopware.Component.extend(componentName, config[componentName].component, importFn)
            }

            if (config[componentName].type === 'override') {
                Shopware.Component.override(componentName, importFn)
            }

            return
        }

        Shopware.Component.register(componentName, importFn)
    })
