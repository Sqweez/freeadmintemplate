const mix = require('laravel-mix');
const config = require('./webpack.config');

mix.options({
    hmrOptions: {
        host: 'localhost',
        port: 8080,
    },
});

mix.js('resources/js/app.js', 'public/js');
mix.js('resources/js/fit.js', 'public/js');
mix.sass('resources/sass/app.scss', 'public/css')
    .webpackConfig(config)
    .options({
        processCssUrls: false,
    });
if (mix.inProduction()) {
    mix.version();
}
