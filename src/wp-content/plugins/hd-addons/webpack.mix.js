let mix = require('laravel-mix');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const dir = './wp-content/plugins/' + directory;

const resources = dir + '/resources';
const assets = dir + '/assets';
const node_modules = './node_modules';

mix.disableNotifications();

mix.copy(`${node_modules}/select2/dist/css/select2.min.css`, `${assets}/css`);
mix.copy(`${node_modules}/select2/dist/js/select2.full.min.js`, `${assets}/js`);

mix.sass(resources + '/sass/addon.scss', assets + '/css');

mix.js(resources + '/js/admin_custom_order.js', assets + '/js')
    .js(resources + '/js/lazyload.js', assets + '/js')
    .js(resources + '/js/addon.js', assets + '/js');
