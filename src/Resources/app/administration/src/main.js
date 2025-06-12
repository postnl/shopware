void import('./core/postnl')
    .then(module => module.default)
    .then(({ PostNLInstance }) => {
        window.PostNLShopware = PostNLInstance

        import('./app/init')
    })