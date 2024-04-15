<?php

namespace Themes;

use Cores\Helper;

use Plugins\ACF\ACF;
use Plugins\CF7;
use Plugins\Editor\TinyMCE;
use Plugins\RankMath;
use Plugins\WpRocket;

\defined( 'ABSPATH' ) || die;

/**
 * Theme Class
 *
 * @author HD
 */
final class Theme {
	public function __construct() {

		// init is run before wp_loaded
		add_action( 'init', [ &$this, 'init' ], 10 );

		add_action( 'after_setup_theme', [ &$this, 'after_setup_theme' ], 10 );
		add_action( 'after_setup_theme', [ &$this, 'plugins_setup' ], 11 );

		/** Widgets wordpress */
		add_action( 'widgets_init', [ &$this, 'unregister_widgets' ], 11 );
		add_action( 'widgets_init', [ &$this, 'register_widgets' ], 11 );

		add_action( 'wp_enqueue_scripts', [ &$this, 'wp_enqueue_scripts' ], 91 );

		// Prevent Specific Plugins from deactivation, delete, v.v...
		add_filter( 'plugin_action_links', [ &$this, 'plugin_action_links' ], 11, 4 );
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
		load_theme_textdomain( HD_TEXT_DOMAIN, trailingslashit( WP_LANG_DIR ) . 'themes/' );
		load_theme_textdomain( HD_TEXT_DOMAIN, get_template_directory() . '/languages' );
		load_theme_textdomain( HD_TEXT_DOMAIN, get_stylesheet_directory() . '/languages' );

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

		/** Gutenberg wide images. */
		add_theme_support( 'align-wide' );

		/** Add support for block styles. */
		add_theme_support( 'wp-block-styles' );

		/** This theme styles the visual editor to resemble the theme style. */
		add_editor_style();

		/** Remove Template Editor support until WP 5.9 since more Theme Blocks are going to be introduced. */
		remove_theme_support( 'block-templates' );

		/** Enable excerpt to page */
		add_post_type_support( 'page', 'excerpt' );

		/** Set default values for the upload media box */
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
	public function init(): void {

		if ( is_admin() ) {
			( new Admin() );
		} else {
			( new Fonts() );
		}

		( new Login() );
		( new Customizer() );
		( new Optimizer() );
		( new Security() );
		( new Options() );

		/** template hooks */
		$this->_hooks();

		( new Shortcode() )::init();

		// folders
		$dirs = [
			'template_structures' => HD_THEME_PATH . 'template-structures',
			'templates'           => HD_THEME_PATH . 'templates',
			'template_parts'      => HD_THEME_PATH . 'template-parts',
			'storage'             => HD_THEME_PATH . 'storage',
			'languages'           => HD_THEME_PATH . 'languages',

			'inc_tpl'  => HD_THEME_PATH . 'inc/tpl',
			'inc_ajax' => HD_THEME_PATH . 'inc/ajax',
		];

		foreach ( $dirs as $dir => $path ) {
			wp_mkdir_p( $path );

			if ( 'template_structures' == $dir ) {
				Helper::FQN_Load( $path, true );
			}
		}
	}

	/** ---------------------------------------- */

	/**
	 * Registers a WP_Widget widget
	 *
	 * @return void
	 */
	public function register_widgets(): void {
		$widgets_dir = HD_THEME_PATH . 'inc/src/Widgets';
		$FQN         = '\\Widgets\\';

		wp_mkdir_p( $widgets_dir );
		Helper::FQN_Load( $widgets_dir, false, true, $FQN, true );
	}

	/** ---------------------------------------- */

	/**
	 * Unregisters a WP_Widget widget
	 *
	 * @return void
	 */
	public function unregister_widgets(): void {

		// Removes the styling added to the header for recent comments
		global $wp_widget_factory;

		remove_action( 'wp_head', [
			$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
			'recent_comments_style'
		] );
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function plugins_setup(): void {

		/** TinyMCE Editor */
		( new TinyMCE() );

		/** ACF */
		if ( ! Helper::is_acf_active() && ! Helper::is_acf_pro_active() ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_acf' ] );
		} else {
			( new ACF() );
		}

		/** WpRocket */
		defined( 'WP_ROCKET_VERSION' ) && ( new WpRocket() );

		/** RankMath */
		class_exists( '\RankMath' ) && ( new RankMath() );

		/** Contact form 7 */
		class_exists( '\WPCF7' ) && ( new CF7() );
	}

	/** ---------------------------------------- */

	/**
	 * Handles admin notice for non-active
	 *
	 * @return void
	 */
	public function admin_notice_missing_acf(): void {
		$class   = 'notice notice-error';
		$message = sprintf( __( 'You need %1$s"Advanced Custom Fields"%2$s for the %1$s"HD theme"%2$s to work and updated.', HD_TEXT_DOMAIN ), '<strong>', '</strong>' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
	}

	/** ---------------------------------------- */

	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	public function wp_enqueue_scripts(): void {

		/** Stylesheet */
		wp_register_style( "plugins-style", get_template_directory_uri() . "/assets/css/plugins.css", [], HD_THEME_VERSION );
		wp_enqueue_style( "app-style", get_template_directory_uri() . "/assets/css/app.css", [ "plugins-style" ], HD_THEME_VERSION );

		/** Scripts */
		wp_enqueue_script( "app", get_template_directory_uri() . "/assets/js/app.js", [ "jquery-core" ], HD_THEME_VERSION, true );
		wp_script_add_data( "app", "defer", true );

		/** Extra */
		wp_enqueue_style( "fonts-style", get_template_directory_uri() . "/assets/css/fonts.css", [], HD_THEME_VERSION );

		wp_enqueue_script( "back-to-top", get_template_directory_uri() . "/assets/js/plugins/back-to-top.js", [], HD_THEME_VERSION, true );
		wp_enqueue_script( "social-share", get_template_directory_uri() . "/assets/js/plugins/social-share.js", [], '0.0.2', true );

		/** Inline Js */
		$l10n = [
			'ajaxUrl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
			'baseUrl'      => trailingslashit( site_url() ),
			'themeUrl'     => trailingslashit( get_template_directory_uri() ),
			'smoothScroll' => ! 0,
			'tracking'     => ( defined( 'TRACKING' ) && TRACKING ) ? 1 : 0,
			'locale'       => get_locale(),
			'lang'         => Helper::getLang(),
			'lg'           => [
				'view_more'   => __( 'View more', HD_TEXT_DOMAIN ),
				'view_detail' => __( 'Detail', HD_TEXT_DOMAIN ),
			],
		];
		wp_localize_script( 'jquery-core', HD_TEXT_DOMAIN, $l10n );

		/** Comments */
		if ( is_singular() && comments_open() && Helper::getOption( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		} else {
			wp_dequeue_script( 'comment-reply' );
		}
	}

	/** ---------------------------------------- */

	/**
	 * @param $actions
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $context
	 *
	 * @return mixed
	 */
	public function plugin_action_links( $actions, $plugin_file, $plugin_data, $context ): mixed {
		$keys = [
			'deactivate',
			'delete'
		];

		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $actions )
			     && in_array(
				     $plugin_file,
				     [
					     //'advanced-custom-fields-pro/acf.php',
				     ] )
			) {
				unset( $actions[ $key ] );
			}
		}

		return $actions;
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
		add_filter( 'big_image_size_threshold', '__return_false' );

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


