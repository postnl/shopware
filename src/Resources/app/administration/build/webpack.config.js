const { join, resolve } = require('path');

module.exports = ({ config }) => {

    const iconPath = resolve(join(__dirname, '..', 'src', 'app', 'assets', 'icons'));

    // Find the url loader rule
    const urlLoaderRule = config.module.rules.find(r => r.loader === 'url-loader' && r.test.test('.png'));
    const svgLoaderRule = config.module.rules.find(r => r.loader === 'svg-inline-loader');

    // Add our svg flags
    urlLoaderRule.exclude.push(iconPath);
    svgLoaderRule.include.push(iconPath);

    return {
        resolve: {
            alias: {
                'qs': resolve(
                    join(__dirname, '..', 'node_modules', 'qs')
                )
            }
        },
    };
}
