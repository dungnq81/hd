<?php

namespace Plugins;

\defined( 'ABSPATH' ) || die;

/**
 * Elementor Plugins
 *
 * @author   WEBHD
 */
final class Elementor {

	// --------------------------------------------------

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ], 99 );

		// Custom hooks
		$this->_hooks();
	}

	// --------------------------------------------------

	/**
	 * @return void
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_style( '_ele-style', ASSETS_URL . "css/plugins/elementor.css", [ "app-style" ], THEME_VERSION );
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	private function _hooks(): void {}
}
