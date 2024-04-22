<?php

\defined( 'ABSPATH' ) || die;

/**
 * Addons Class
 *
 * @author HD
 */
final class Addons {
	public function __construct() {
		add_action( 'init', [ &$this, 'i18n' ], 10 );
	}

	/**
	 * Load localization file
	 *
	 * @return void
	 */
	public function i18n(): void {
		load_plugin_textdomain( ADDONS_TEXT_DOMAIN );
		load_plugin_textdomain( ADDONS_TEXT_DOMAIN, false, ADDONS_PATH . 'languages' );
	}
}