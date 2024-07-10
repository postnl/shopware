const { Component } = Shopware;

// TODO Shopware 6.7: Create copy of mt-icon instead, as that is a basic vue component and cannot be overridden
Component.override(
    Component.getComponentRegistry().has('sw-icon-deprecated') ? 'sw-icon-deprecated' : 'sw-icon',
    {
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
    }
);