void import('./core/postnl').then(({ PostNLInstance }) => {
    window.PostNLShopware = PostNLInstance

    import('./app/init')
})