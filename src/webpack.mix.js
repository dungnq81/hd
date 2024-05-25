const mix = require('laravel-mix');
const { globSync } = require('glob');

mix.webpackConfig({
    stats: {
        children: true,
    },
    watchOptions: {
        ignored: '/node_modules/',
        poll: false,
    },
    externals: {
        $: 'jQuery',
        jquery: 'jQuery',
    },
}).options({
    processCssUrls: false,
    clearConsole: true,
    terser: {
        extractComments: true,
    },
    autoprefixer: {
        remove: false,
    },
});

// Source maps when not in production.
if (!mix.inProduction()) {
    mix.sourceMaps(false, 'source-map');
}

// Run only for a plugin.
require('./wp-content/plugins/hd-addons/webpack.mix.js');

// Run only for themes.
globSync('./wp-content/themes/**/webpack.mix.js').forEach((file) => require(`./${file}`));
