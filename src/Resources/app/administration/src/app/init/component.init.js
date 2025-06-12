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
        const allowedTypes = ['extend', 'override']

        if (config?.[componentName]) {
            if (!'type' in config[componentName] || !allowedTypes.includes(config[componentName].type)) {
                throw `Missing "type" field in ${ componentName } configuration. Must be one of ${ allowedTypes.join(', 0') }`
            }

            if (config[componentName].type === 'extend') {
                if (!'component' in config[componentName]) {
                    throw `Missing "component" field in ${ componentName } configuration.`
                }

                // console.log(`Component ${componentName} extends ${config[componentName].component}`)
                Shopware.Component.extend(componentName, config[componentName].component, importFn)
            }

            if (config[componentName].type === 'override') {
                // console.log(`Overriding component ${componentName}`)
                Shopware.Component.override(componentName, importFn)
            }

            return
        }

        // console.log(`Registering component ${componentName}`)
        Shopware.Component.register(componentName, importFn)
    })
