<?php

namespace Plugins\WooCommerce;

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

/**
 * WooCommerce Plugin
 *
 * @author   WEBHD
 */
final class WooCommerce {

	/**
	 * @var array|false|mixed
	 */
	public mixed $woocommerce_options = [];

	// ------------------------------------------------------

	public function __construct() {

		$this->woocommerce_options = Helper::getOption( 'woocommerce__options', false, false );
		$woocommerce_jsonld        = $this->woocommerce_options['woocommerce_jsonld'] ?? '';
		if ( $woocommerce_jsonld ) {

			// Remove the default WooCommerce 3 JSON/LD structured data format
			add_action( 'init', [ &$this, 'remove_woocommerce_jsonld' ], 10 );
		}

		add_action( 'after_setup_theme', [ &$this, 'after_setup_theme' ], 33 );

		add_action( 'widgets_init', [ &$this, 'unregister_default_widgets' ], 33 );
		add_action( 'widgets_init', [ &$this, 'register_widgets' ], 33 );

		add_action( 'enqueue_block_assets', [ &$this, 'enqueue_block_assets' ], 41 );
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ], 98 );

		// hooks
		$this->_hooks();
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function remove_woocommerce_jsonld(): void {
		remove_action( 'wp_footer', [ WC()->structured_data, 'output_structured_data' ], 10 );
		remove_action( 'woocommerce_email_order_details', [
			WC()->structured_data,
			'output_email_structured_data'
		], 30 );
	}

	// ------------------------------------------------------

	/**
	 * Registers a WP_Widget widget
	 *
	 * @return void
	 */
	public function register_widgets(): void {
		$widgets_dir = INC_PATH . 'src/Plugins/WooCommerce/Widgets';
		$FQN         = '\\Plugins\\WooCommerce\\Widgets\\';

		Helper::createDirectory( $widgets_dir );
		Helper::FQN_Load( $widgets_dir, false, true, $FQN, true );
	}

	// ------------------------------------------------------

	/**
	 * Unregisters a WP_Widget widget
	 *
	 * @return void
	 */
	public function unregister_default_widgets(): void {
		unregister_widget( 'WC_Widget_Product_Search' );
		unregister_widget( 'WC_Widget_Products' );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {

		// remove 'woocommerce-inline-inline-css'
		$woocommerce_default_css = $this->woocommerce_options['woocommerce_default_css'] ?? '';
		if ( $woocommerce_default_css ) {
			wp_deregister_style( 'woocommerce-inline' );
		}

		wp_enqueue_style( 'hdwc-style', ASSETS_URL . "css/plugins/woocommerce.css", [ "app-style" ], THEME_VERSION );
		wp_enqueue_script( "hdwc", ASSETS_URL . "js/plugins/woocommerce.js", [ "app" ], THEME_VERSION, true );
		wp_script_add_data( "hdwc", "defer", true );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function after_setup_theme(): void {
		add_theme_support( 'woocommerce' );

		// Add support for WC features.
		//add_theme_support( 'wc-product-gallery-zoom' );
		//add_theme_support( 'wc-product-gallery-lightbox' );
		//add_theme_support( 'wc-product-gallery-slider' );

		// Remove woocommerce default styles
		$woocommerce_default_css = $this->woocommerce_options['woocommerce_default_css'] ?? '';
		if ( $woocommerce_default_css ) {
			add_filter( 'woocommerce_enqueue_styles', '__return_false' );
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function enqueue_block_assets(): void {
		global $wp_styles;

		// Remove woocommerce blocks styles
		$block_editor_options = Helper::getOption( 'block_editor__options', false, true );

		if ( $block_editor_options['block_style_off'] ?? '' ) {

			wp_deregister_style( 'wc-block-editor' );

			wp_deregister_style( 'wc-blocks-style' );
			wp_deregister_style( 'wc-blocks-packages-style' );

			$styles_to_remove = [];
			foreach ( $wp_styles->registered as $handle => $style ) {
				if ( str_starts_with( $handle, 'wc-blocks-style-' ) ) {
					$styles_to_remove[] = $handle;
				}
			}

			foreach ( $styles_to_remove as $handle ) {
				wp_deregister_style( $handle );
			}
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	protected function _hooks(): void {

		// https://stackoverflow.com/questions/57321805/remove-header-from-the-woocommerce-administrator-panel
		add_action( 'admin_head', function () {
			echo '<style>#wpadminbar ~ #wpbody { margin-top: 0 !important; }.woocommerce-layout__header { display: none !important; }</style>';
		} );
	}
}
