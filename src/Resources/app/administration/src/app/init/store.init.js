const stores = import.meta.glob('./../store/!(*.spec).{j,t}s', { eager: true, import: 'default' })

Object.values(stores).forEach(store => Shopware.Store.register(store))