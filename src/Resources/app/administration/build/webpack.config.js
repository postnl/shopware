const { join, resolve } = require('path');

module.exports = () => {
    return {
        resolve: {
            alias: {
                'qs': resolve(
                    join(__dirname, '..', 'node_modules', 'qs')
                )
            }
        }
    };
}
