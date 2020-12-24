const mix = require('laravel-mix');
const config = require('./webpack.config');


mix.js('resources/js/app.js', 'public/js');
mix.sass('resources/sass/app.scss', 'public/css')
    .webpackConfig(config)
    .options({
        processCssUrls: false
    })

if (mix.inProduction()) {
    mix.version();

}

