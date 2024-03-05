const defaultConfig = require('@wordpress/scripts/config/webpack.config.js');
const WooCommerceDependencyExtractionWebpackPlugin = require('@woocommerce/dependency-extraction-webpack-plugin');
const path = require('path');

const wcDepMap = {
    '@woocommerce/blocks-registry': ['wc', 'wcBlocksRegistry'],
    '@woocommerce/settings': ['wc', 'wcSettings']
};

const wcHandleMap = {
    '@woocommerce/blocks-registry': 'wc-blocks-registry',
    '@woocommerce/settings': 'wc-settings'
};

const requestToExternal = (request) => {
    if (wcDepMap[request]) {
        return wcDepMap[request];
    }
};

const requestToHandle = (request) => {
    if (wcHandleMap[request]) {
        return wcHandleMap[request];
    }
};

// Export configuration.
module.exports = {
    ...defaultConfig,
    entry: {
        'frontend/blocks': '/resources/js/frontend/index.js',
        'frontend/bank-account-form': '/resources/js/frontend/bank-account-form.js',
        'frontend/wallets-form': '/resources/js/frontend/wallets-form.js',
        'frontend/apms': '/resources/js/frontend/apms.js',
    },
    output: {
        path: path.resolve(__dirname, 'assets/build/js'),
        filename: '[name].js',
    },
    plugins: [
        ...defaultConfig.plugins.filter(
            (plugin) =>
                plugin.constructor.name !== 'DependencyExtractionWebpackPlugin'
        ),
        new WooCommerceDependencyExtractionWebpackPlugin({
            requestToExternal,
            requestToHandle
        })
    ]
};