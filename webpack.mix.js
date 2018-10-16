let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .react('resources/assets/js/app.js', 'public/build/js')
    .copy('resources/assets/js/customer/order/index.js', 'public/build/js/customer/order')
    .copy('resources/assets/js/customer/order/create.js', 'public/build/js/customer/order')
    .copy('resources/assets/js/customer/order/edit.js', 'public/build/js/customer/order')
    .copy('resources/assets/js/customer/order/view.js', 'public/build/js/customer/order')
    .copy('resources/assets/js/customer/order/deposit.js', 'public/build/js/customer/order')
    .copy('resources/assets/js/customer/order/deposit.js', 'public/build/js/customer/order')
    .copy('resources/assets/js/customer/order/bill.js', 'public/build/js/customer/order')
    .sass('resources/assets/sass/app.scss', 'public/build/css')
    .sass('resources/assets/sass/customers.scss', 'public/build/css');
    // .browserSync({
    //     proxy: "http://dev.redex.vn",
    //     open: false
    // });

// if (mix.inProduction()) {
//     mix.version();
// }
mix.version(); // always
