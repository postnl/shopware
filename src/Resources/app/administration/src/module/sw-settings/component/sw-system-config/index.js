Shopware.Component.override('sw-system-config', {
    methods: {
        hasMapInheritanceSupport(element) {
            const componentName = element.config && element.config.componentName
                ? element.config.componentName
                : ""

            if(componentName.startsWith('memo') || componentName.startsWith('postnl')) {
                return true;
            }

            return this.$super('hasMapInheritanceSupport', element);
        }
    }
})
