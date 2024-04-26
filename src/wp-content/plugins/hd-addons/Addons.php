<?php

use Addons\Base_Slug\Base_Slug;
use Addons\Custom_Email\Custom_Email;
use Addons\Custom_Order\Custom_Order;
use Addons\Heartbeat\Heartbeat;
use Addons\LazyLoad\LazyLoad;
use Addons\SMTP\SMTP;
use Addons\SVG\SVG;

\defined( 'ABSPATH' ) || die;

/**
 * Addons Class
 *
 * @author HD Team
 */
final class Addons {
	public function __construct() {
		add_action( 'plugins_loaded', [ &$this, 'i18n' ] );
		add_action( 'plugins_loaded', [ &$this, 'plugins_loaded' ] );

		add_action( 'init', [ &$this, 'init' ] );
		add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ], 39 );

		add_action( 'admin_menu', [ &$this, 'admin_menu' ] );
	}

	/** ---------------------------------------- */

	/**
	 * Load localization file
	 *
	 * @return void
	 */
	public function i18n(): void {
		load_plugin_textdomain( ADDONS_TEXT_DOMAIN );
		load_plugin_textdomain( ADDONS_TEXT_DOMAIN, false, ADDONS_PATH . 'languages' );
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function admin_enqueue_scripts(): void {
		if ( ! wp_style_is( 'woocommerce_admin_styles' ) ) {
			wp_enqueue_style( "select2-style", ADDONS_URL . "assets/css/select2.min.css", [], ADDONS_VERSION );
		}

		if ( ! wp_script_is( 'select2', 'registered' ) ) {
			wp_register_script( "select2", ADDONS_URL . "assets/js/select2.full.min.js", [ "jquery-core" ], ADDONS_VERSION );
		}

		wp_enqueue_style( "addon-style", ADDONS_URL . "assets/css/addon.css", [], ADDONS_VERSION );
		wp_enqueue_script( "addon", ADDONS_URL . "assets/js/addon.js", [ "jquery-core" ], ADDONS_VERSION, true );
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function init(): void {}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function plugins_loaded(): void {

		( new Custom_Order() );
		( new Custom_Email() );
		( new SMTP() );
		( new SVG() );
		( new LazyLoad() );
		( new Base_Slug() );

		( new Heartbeat() );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function admin_menu(): void {
		remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
	}
}
