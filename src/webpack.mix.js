const { globSync } = require('glob');
const mix = require('laravel-mix');

mix.webpackConfig({
        stats: {
            children: true,
        },
        watchOptions: {
            ignored: '/node_modules/',
            poll: false,
        }
    })
    .options({
        processCssUrls: false,
        clearConsole: true,
        terser: {
            extractComments: true,
        },
        autoprefixer: {
            remove: false
        }
    });

// Source maps when not in production.
if ( !mix.inProduction() ) {
    mix.sourceMaps(false, 'source-map');
}

// Run only for a plugin.

// Run only for themes.
globSync('./wp-content/themes/**/webpack.mix.js').forEach(file => require(`./${file}`));
