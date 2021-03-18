const path = require('path')
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    plugins: [
        new MomentLocalesPlugin(),
        new MiniCssExtractPlugin()
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js')
        }
    }
};
