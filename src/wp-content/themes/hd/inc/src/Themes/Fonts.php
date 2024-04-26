<?php

namespace Themes;

\defined( 'ABSPATH' ) || die;

/**
 * Fonts Class
 *
 * @author HD
 */
final class Fonts {
	public function __construct() {
		add_action( 'wp_head', [ &$this, 'pre_connect' ], 9 );
		add_action( 'wp_enqueue_scripts', [ &$this, 'enqueue_scripts' ], 101 );
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function pre_connect(): void {}

	/** ---------------------------------------- */

	public function enqueue_scripts(): void {
		wp_enqueue_style( "fonts-style", get_template_directory_uri() . "/assets/css/fonts.css", [], THEME_VERSION );

		//wp_register_script( "fontawesome-kit", "https://kit.fontawesome.com/870d5b0bdf.js", [], false, true );
		//wp_script_add_data( "fontawesome-kit", "defer", true );
		//wp_enqueue_script( "fontawesome-kit" );
	}
}
