const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    entry: {
        index: './src/js/index.js'
    },
    mode: 'production',
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'js/mod_einsatz_stats.js'
    },
    optimization: {
        minimizer: [new TerserPlugin({
            extractComments: false,
        })],
    }
};
