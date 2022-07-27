const { Mixin } = Shopware;

Mixin.register('postnl-memo-grid-span', {
    methods: {
        span(columns) {
            return {
                'grid-column': `span ${ columns }`
            }
        }
    }
});
