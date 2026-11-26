const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/page/home.js', 'public/js/page')
    .js('resources/js/page/history.js', 'public/js/page')
    .postCss('resources/css/page/home.css', 'public/css', []);
