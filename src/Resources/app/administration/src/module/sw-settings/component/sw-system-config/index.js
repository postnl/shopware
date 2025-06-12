import './sw-system-config.scss'

export default {
    methods: {
        hasMapInheritanceSupport(element) {
            const componentName = element.config && element.config.componentName
                ? element.config.componentName
                : ""

            if(componentName.startsWith('postnl')) {
                return true;
            }

            return this.$super('hasMapInheritanceSupport', element);
        }
    }
}
