<?php

namespace Themes;

use Cores\Helper;
use Plugins\ACF\ACF;
use Plugins\CF7;
use Plugins\Editor\TinyMCE;
use Plugins\RankMath;
use Plugins\WooCommerce\WooCommerce;
use Plugins\WpRocket;

\defined( 'ABSPATH' ) || die;

/**
 * Theme Class
 *
 * @author HD
 */

final class Theme {
	public function __construct() {

//		global $wp_post_types;
//		foreach ( $wp_post_types as $type => $custom_post ) {
//			if (
//			     $custom_post->public === true &&
//			     $custom_post->show_ui === true
//			) {
//				dump($custom_post);
//			}
//		}
//
//		dump($wp_post_types);

		// plugins_loaded -> after_setup_theme -> init -> widgets_init -> wp_loaded -> admin_menu -> admin_init ...

		add_action( 'after_setup_theme', [ &$this, 'after_setup_theme' ], 10 );
		add_action( 'after_setup_theme', [ &$this, 'setup' ], 11 );
		add_action( 'after_setup_theme', [ &$this, 'plugins_setup' ], 12 );

		/** Widgets WordPress */
		add_action( 'widgets_init', [ &$this, 'unregister_widgets' ], 13 );
		add_action( 'widgets_init', [ &$this, 'register_widgets' ], 13 );

		add_action( 'wp_enqueue_scripts', [ &$this, 'wp_enqueue_scripts' ], 10 );
	}

	/** ---------------------------------------- */

	/**
	 * Sets up theme defaults and register support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post-thumbnails.
	 */
	public function after_setup_theme(): void {
		/**
		 * Make theme available for translation.
		 * Translations can be filed at WordPress.org.
		 * See: https://translate.wordpress.org/projects/wp-themes/hello-elementor
		 */
		load_theme_textdomain( TEXT_DOMAIN, trailingslashit( WP_LANG_DIR ) . 'themes/' );
		load_theme_textdomain( TEXT_DOMAIN, get_template_directory() . '/languages' );
		load_theme_textdomain( TEXT_DOMAIN, get_stylesheet_directory() . '/languages' );

		/** Add theme support for various features. */
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', [
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
		] );

		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );

		/** This theme styles the visual editor to resemble the theme style. */
		add_editor_style();

		/** Remove Template Editor support until WP 5.9 since more Theme Blocks are going to be introduced. */
		remove_theme_support( 'block-templates' );

		/** Enable excerpt to page, page-attributes to post */
		add_post_type_support( 'page', [ 'excerpt' ] );
		add_post_type_support( 'post', [ 'page-attributes' ] );

		/** Set default values for the upload media box */
		update_option( 'image_default_align', 'center' );
		update_option( 'image_default_size', 'large' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		$logo_height = 240;
		$logo_width  = 240;

		add_theme_support(
			'custom-logo',
			apply_filters(
				'custom_logo_args',
				[
					'height'               => $logo_height,
					'width'                => $logo_width,
					'flex-height'          => true,
					'flex-width'           => true,
					'unlink-homepage-logo' => false,
				]
			)
		);
	}

	/** ---------------------------------------- */

	/**
	 * Init function
	 *
	 * @return void
	 */
	public function setup(): void {

		if ( is_admin() ) {
			( new Admin() );
		} else {
			( new Fonts() );
		}

		( new Admin_Login() );
		( new Customizer() );
		( new Optimizer() );
		( new Security() );
		( new Options() );

		/** template hooks */
		$this->_hooks();

		( new Shortcode() )::init();

		// folders
		$dirs = [
			'template_structures' => THEME_PATH . 'template-structures',
			'templates'           => THEME_PATH . 'templates',
			'template_parts'      => THEME_PATH . 'template-parts',
			'storage'             => THEME_PATH . 'storage',
			'languages'           => THEME_PATH . 'languages',

			'inc_tpl'  => INC_PATH . 'admin/tpl',
			'inc_ajax' => INC_PATH . 'ajax',
		];

		foreach ( $dirs as $dir => $path ) {
			Helper::createDirectory( $path );

			// autoload template_structures & ajax files
			if ( in_array( $dir, [ 'template_structures', 'inc_ajax' ] ) ) {
				Helper::FQN_Load( $path, true );
			}
		}
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function plugins_setup(): void {

		/** TinyMCE Editor */
		( new TinyMCE() );

		/** WooCommerce */
		Helper::is_woocommerce_active() && ( new WooCommerce() );

		/** ACF */
		if ( Helper::is_acf_active() || Helper::is_acf_pro_active() ) {
			( new ACF() );
		}

		/** Wp-Rocket */
		defined( 'WP_ROCKET_VERSION' ) && ( new WpRocket() );

		/** RankMath */
		class_exists( '\RankMath' ) && ( new RankMath() );

		/** Contact form 7 */
		class_exists( '\WPCF7' ) && ( new CF7() );
	}

	/** ---------------------------------------- */

	/**
	 * Registers a WP_Widget widget
	 *
	 * @return void
	 */
	public function register_widgets(): void {
		$widgets_dir = INC_PATH . 'src/Widgets';
		$FQN         = '\\Widgets\\';

		Helper::createDirectory( $widgets_dir );
		Helper::FQN_Load( $widgets_dir, false, true, $FQN, true );
	}

	/** ---------------------------------------- */

	/**
	 * Unregisters a WP_Widget widget
	 *
	 * @return void
	 */
	public function unregister_widgets(): void {
		unregister_widget( 'WP_Widget_Search' );
		unregister_widget( 'WP_Widget_Recent_Posts' );

		// Removes the styling added to the header for recent comments
		global $wp_widget_factory;

		remove_action( 'wp_head', [
			$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
			'recent_comments_style'
		] );
	}

	/** ---------------------------------------- */

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts(): void {

		// wp_enqueue_style( 'style', get_stylesheet_uri(), [], THEME_VERSION );

		/** Stylesheet */
		wp_register_style( "plugins-style", ASSETS_URL . "css/plugins.css", [], THEME_VERSION );
		wp_enqueue_style( "app-style", ASSETS_URL . "css/app.css", [ "plugins-style" ], THEME_VERSION );

		/** Scripts */
		wp_enqueue_script( "app", ASSETS_URL . "js/app.js", [ "jquery-core" ], THEME_VERSION, true );
		wp_script_add_data( "app", "defer", true );

		wp_enqueue_style( "fonts-style", ASSETS_URL . "css/fonts.css", [], THEME_VERSION );

		wp_enqueue_script( "back-to-top", ASSETS_URL . "js/plugins/back-to-top.js", [], THEME_VERSION, true );
		wp_enqueue_script( "social-share", ASSETS_URL . "js/plugins/social-share.js", [], '0.0.3', true );

		/** Inline Js */
		$l10n = [
			'ajaxUrl'      => esc_js( admin_url( 'admin-ajax.php', 'relative' ) ),
			'baseUrl'      => esc_js( untrailingslashit( site_url() ) . '/' ),
			'themeUrl'     => esc_js( THEME_URL ),
			'_wpnonce'     => wp_create_nonce( '_wpnonce_ajax_csrf' ),
			'smoothScroll' => ! 0,
			'tracking'     => ( defined( 'TRACKING' ) && TRACKING ) ? 1 : 0,
			'locale'       => esc_js( get_locale() ),
			'lang'         => esc_js( Helper::getLang() ),
			'lg'           => [
				'view_more'   => __( 'View more', TEXT_DOMAIN ),
				'view_detail' => __( 'Detail', TEXT_DOMAIN ),
			],
		];
		wp_localize_script( 'jquery-core', TEXT_DOMAIN, $l10n );

		/** Comments */
		if ( is_singular() && comments_open() && Helper::getOption( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		} else {
			wp_dequeue_script( 'comment-reply' );
		}
	}

	/** ---------------------------------------- */

	protected function _hooks(): void {
		/**
		 * Use the is-active class of ZURB Foundation on wp_list_pages output.
		 * From required+ Foundation http://themes.required.ch.
		 */
		add_filter( 'wp_list_pages', function ( $input ) {
			$pattern = '/current_page_item/';
			$replace = 'current_page_item is-active';

			return preg_replace( $pattern, $replace, $input );
		}, 10, 2 );

		/** Add support for buttons in the top-bar menu */
		add_filter( 'wp_nav_menu', function ( $ul_class ) {
			$find    = [ '/<a rel="button"/', '/<a title=".*?" rel="button"/' ];
			$replace = [ '<a rel="button" class="button"', '<a rel="button" class="button"' ];

			return preg_replace( $find, $replace, $ul_class, 1 );
		} );

		// -------------------------------------------------------------
		// images sizes
		// -------------------------------------------------------------

		/**
		 * thumbnail (480x0)
		 * medium (768x0)
		 * large (1024x0)
		 *
		 * small-thumbnail (150x150)
		 * widescreen (1920x9999)
		 * post-thumbnail (1200x9999)
		 */

		/** Custom thumb */
		add_image_size( 'small-thumbnail', 150, 150, true );
		add_image_size( 'widescreen', 1920, 9999, false );
		add_image_size( 'post-thumbnail', 1200, 9999, false );

		/** Disable unwanted image sizes */
		add_filter( 'intermediate_image_sizes_advanced', function ( $sizes ) {
			unset( $sizes['medium_large'] );

			unset( $sizes['1536x1536'] ); // disable 2x medium-large size
			unset( $sizes['2048x2048'] ); // disable 2x large size

			return $sizes;
		} );

		/** Disable scaled */
		//add_filter( 'big_image_size_threshold', '__return_false' );

		/** Disable other sizes */
		add_action( 'init', function () {
			remove_image_size( '1536x1536' ); // disable 2x medium-large size
			remove_image_size( '2048x2048' ); // disable 2x large size
		} );

		// ------------------------------------------

		add_filter( 'post_thumbnail_html', function ( $html ) {
			return preg_replace( '/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html );
		}, 10, 1 );

		add_filter( 'image_send_to_editor', function ( $html ) {
			return preg_replace( '/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html );
		}, 10, 1 );

		add_filter( 'the_content', function ( $html ) {
			return preg_replace( '/(<img[^>]+)(style=\"[^\"]+\")([^>]+)(>)/', '${1}${3}${4}', $html );
		}, 10, 1 );
	}
}
