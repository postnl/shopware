import { h as createElement } from 'vue'

const assets = import.meta.glob('./../app/assets/**/*.svg', {
    eager: false,
    query: '?raw',
    import: 'default',
})

Object
    .entries(assets)
    .forEach(([path, svg]) => {
        const componentName = path.split('/').slice(3).join('-').split('.').shift()
        const name = `postnl-asset-${ componentName }`

        const promise = typeof svg === 'function' ? svg : () => Promise.resolve(svg);

        Shopware.Component.register(
            name,
            () => promise().then((svg) => {
                return {
                    name,
                    functional: true,
                    render(elementContext) {
                        return createElement('span', {
                            ...elementContext.$attrs,
                            innerHTML: svg,
                        });
                    },
                }
            })
        )
    })
