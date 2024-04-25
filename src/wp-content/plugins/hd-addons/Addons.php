<?php

use Addons\Admin;
use Addons\Custom_Order\Custom_Order;
use Addons\SMTP\SMTP;
use Addons\SVG\SVG;

\defined( 'ABSPATH' ) || die;

/**
 * Addons Class
 *
 * @author HD
 */
final class Addons {
	public function __construct() {
		add_action( 'plugins_loaded', [ &$this, 'i18n' ] );
		add_action( 'plugins_loaded', [ &$this, 'plugins_loaded' ] );

		add_action( 'init', [ &$this, 'init' ] );
		add_action( 'admin_enqueue_scripts', [ &$this, 'enqueue_admin_scripts' ] );
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
	public function enqueue_admin_scripts(): void {
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function init(): void {
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function plugins_loaded(): void {
		if ( is_admin() ) {
			( new Admin() );
		}

		( new Custom_Order() );
		( new SMTP() );
		( new SVG() );
	}
}