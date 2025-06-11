const services = import.meta.glob('./../../core/service/api/*.service.js', { eager: true, import: 'default' })

Object
    .values(services)
    .forEach(serviceClass => {
        const initContainer = Shopware.Application.getContainer('init')
        const service = new serviceClass(initContainer.httpClient, Shopware.Service('loginService'))

        const serviceName = service.name || serviceClass.name

        Shopware.ApiService.register(serviceName, service)
        Shopware.Application.addServiceProvider(serviceName, () => service)
    })