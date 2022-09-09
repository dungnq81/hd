<?php

namespace APX\Themes;

\defined( '\WPINC' ) || die;

/**
 * Theme Class
 * @author APX
 */

if ( ! class_exists( 'Theme' ) ) {
    class Theme {
        public function __construct() {

            add_action( 'after_setup_theme', [ &$this, 'after_setup_theme' ] );
        }

        /** ---------------------------------------- */

        /**
         * Init function
         *
         * @return void
         */
        public function init() {

            if ( is_admin() ) {
                ( new Admin );
            } else {
                ( new Fonts );
            }
        }

        /** ---------------------------------------- */

        /**
         * Sets up theme defaults and registers support for various WordPress features.
         *
         * Note that this function is hooked into the after_setup_theme hook, which
         * runs before the init hook. The init hook is too late for some features, such
         * as indicating support for post thumbnails.
         */
        public function after_setup_theme() {

            /**
             * Make theme available for translation.
             * Translations can be filed at WordPress.org.
             * See: https://translate.wordpress.org/projects/wp-themes/hello-elementor
             */
            load_theme_textdomain( 'apx', trailingslashit( WP_LANG_DIR ) . 'themes/' );
            load_theme_textdomain( 'apx', get_stylesheet_directory() . '/languages' );
            load_theme_textdomain( 'apx', get_template_directory() . '/languages' );

            // Add theme support for various features.
            add_theme_support( 'automatic-feed-links' );
            add_theme_support( 'post-thumbnails' );

            //add_theme_support( 'post-formats', [ 'aside', 'image', 'gallery', 'video', 'quote', 'link', 'status' ] );
            add_theme_support( 'title-tag' );
            add_theme_support( 'html5', [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'script',
                'style',
                'navigation-widgets'
            ] );

            add_theme_support( 'customize-selective-refresh-widgets' );
            add_theme_support( 'align-wide' );
            add_theme_support( 'responsive-embeds' );

            // Add support for block styles.
            add_theme_support( 'wp-block-styles' );

            // This theme styles the visual editor to resemble the theme style.
            add_theme_support( 'editor-styles' );
            add_editor_style( get_template_directory_uri() . "/assets/css/editor-style.css" );

            // Remove Template Editor support until WP 5.9 since more Theme Blocks are going to be introduced.
            remove_theme_support( 'block-templates' );

            // Enable excerpt to page
            add_post_type_support( 'page', 'excerpt' );

            if ( apply_filters( 'responsive_oembed', true ) ) {

                // Filters the oEmbed process to run the responsive_oembed_wrapper() function.
                add_filter( 'embed_oembed_html', [ &$this, 'responsive_oembed_wrapper' ], 10, 3 );
            }

            // Set default values for the upload media box
            update_option( 'image_default_align', 'center' );
            update_option( 'image_default_size', 'large' );

            /**
             * Add support for core custom logo.
             *
             * @link https://codex.wordpress.org/Theme_Logo
             */
            $logo_height = 120;
            $logo_width  = 240;

            add_theme_support(
                'custom-logo',
                apply_filters(
                    'custom_logo_args',
                    [
                        'height'      => $logo_height,
                        'width'       => $logo_width,
                        'flex-height' => true,
                        'flex-width'  => true,
                        'unlink-homepage-logo' => false,
                    ]
                )
            );

            // Adds `async`, `defer` and attribute support for scripts registered or enqueued by the theme.
            $loader = new ScriptLoader;
            add_filter( 'script_loader_tag', [ &$loader, 'filterScriptTag' ], 10, 3 );
        }

        /** ---------------------------------------- */


    }
}