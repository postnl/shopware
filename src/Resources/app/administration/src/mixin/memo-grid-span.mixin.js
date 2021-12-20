const { Mixin } = Shopware;

Mixin.register('memo-grid-span', {
    methods: {
        span(columns) {
            return {
                'grid-column': `span ${ columns }`
            }
        }
    }
});
