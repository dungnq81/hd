const mix = require('laravel-mix');
const path = require('path');

const directory = path.basename(path.resolve(__dirname));
const dir = `./wp-content/plugins/${directory}`;
const resources = `${dir}/resources`;
const assets = `${dir}/assets`;
const nodeModules = './node_modules';

mix.disableNotifications();

// Copy Select2 library assets
mix.copy(`${nodeModules}/select2/dist/css/select2.min.css`, `${assets}/css`);
mix.copy(`${nodeModules}/select2/dist/js/select2.full.min.js`, `${assets}/js`);

// Compile SASS files
mix.sass(`${resources}/sass/editor-style.scss`, `${assets}/css`);

// Compile JS files
mix.js(`${resources}/js/custom_order.js`, `${assets}/js`)
    .js(`${resources}/js/lazyload.js`, `${assets}/js`)
    .js(`${resources}/js/recaptcha.js`, `${assets}/js`);
