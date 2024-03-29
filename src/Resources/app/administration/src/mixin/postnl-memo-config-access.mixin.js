const { Mixin } = Shopware;

Mixin.register('postnl-memo-config-access', {
    computed: {
        systemConfig() {
            let component = this;
            while(!component.domain) {
                component = component.$parent;
            }
            return component;
        },
        systemConfigDomain() {
            return this.systemConfig.domain;
        },
        isDefaultSalesChannel() {
            return this.systemConfig.currentSalesChannelId === null;
        },
        currentConfigData() {
            return this.systemConfig.actualConfigData[this.systemConfig.currentSalesChannelId];
        },
        inheritedConfigData() {
            return this.systemConfig.actualConfigData[null];
        }
    },

    methods: {
        getConfigItem(name) {
            const configName = `${ this.systemConfigDomain }.${ name }`;
            if (!this.isDefaultSalesChannel &&
                this.currentConfigData.hasOwnProperty(configName) &&
                this.currentConfigData.hasOwnProperty(configName) !== null) {
                return this.currentConfigData[configName];
            } else {
                return this.inheritedConfigData[configName] ?? null;
            }
        },

        getJsonConfigItem(name) {
            try {
                return JSON.parse(this.getConfigItem(name));
            } catch (e) {
                return null;
            }
        }
    }
});
