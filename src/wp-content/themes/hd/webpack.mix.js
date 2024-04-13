const mix = require('laravel-mix');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const dir = './wp-content/themes/' + directory;

const resources = dir + '/resources';
const assets = dir + '/assets';
const storage = dir + '/storage';

mix
    .disableNotifications()

    .copyDirectory(storage + '/fonts/fontawesome/webfonts', assets + '/webfonts')
    .copyDirectory(resources + '/img', assets + '/img')

    .sass(resources + '/sass/editor-style.scss', assets + '/css')
    .sass(resources + '/sass/admin.scss', assets + '/css')

    .sass(resources + '/sass/fonts.scss', assets + '/css')
    .sass(resources + '/sass/plugins.scss', assets + '/css')
    .sass(resources + '/sass/app.scss', assets + '/css')

    .js(resources + '/js/plugins/lazysizes.js', assets + '/js/plugins')
    .js(resources + '/js/plugins/back-to-top.js', assets + '/js/plugins')
    .js(resources + '/js/plugins/social-share.js', assets + '/js/plugins')
    .js(resources + '/js/plugins/skip-link-focus.js', assets + '/js/plugins')
    .js(resources + '/js/plugins/flex-gap.js', assets + '/js/plugins')
    .js(resources + '/js/plugins/load-scripts.js', assets + '/js/plugins')

    .js(resources + '/js/login.js', assets + '/js')
    .js(resources + '/js/admin.js', assets + '/js')

    .js(resources + '/js/app.js', assets + '/js');
