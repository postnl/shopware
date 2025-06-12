const modules = import.meta.glob('./../../module/*/index.{j,t}s', { eager: true, import: 'default' })

Object.entries(modules).forEach(([path, module])=> Shopware.Module.register(path.split('/').slice(-2).shift(), module))