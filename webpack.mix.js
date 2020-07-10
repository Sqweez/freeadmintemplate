const mix = require('laravel-mix');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

mix.js('resources/js/app.js', 'public/js')//.version();
mix.sass('resources/sass/app.scss', 'public/css')
    .webpackConfig({
        plugins: [
            new MomentLocalesPlugin(),
            new MiniCssExtractPlugin()
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js')
            }
        }
    })