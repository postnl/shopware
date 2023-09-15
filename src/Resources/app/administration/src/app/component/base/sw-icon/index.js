const { Component } = Shopware;

Component.override('sw-icon', {
    methods: {
        loadIconSvgData(variant, iconName, iconFullName) {
            if(variant !== 'postnl') {
                return this.$super('loadIconSvgData', variant, iconName, iconFullName);
            }

            return import(`./../../../assets/icons/${iconFullName}.svg`).then((iconSvgData) => {
                this.iconSvgData = iconSvgData.default;
            });
        },
    },
});