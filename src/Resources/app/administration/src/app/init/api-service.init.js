const services = import.meta.glob('./../../core/service/api/*.service.js', { eager: true, import: 'default' })

Object
    .entries(services)
    .forEach(([path, serviceClass]) => {
        const initContainer = Shopware.Application.getContainer('init')
        const service = new serviceClass(initContainer.httpClient, Shopware.Service('loginService'))

        const serviceName = service.name ||
            path.split('/').pop()
                .split('.').shift()
                .split('-').map(part => part.at(0).toUpperCase() + part.slice(1))
                .join('') + 'Service'

        Shopware.ApiService.register(serviceName, service)
        Shopware.Application.addServiceProvider(serviceName, () => service)
    })