<?php

use Addons\Base_Slug\Base_Slug;
use Addons\Custom_Email\Custom_Email;
use Addons\Custom_Order\Custom_Order;
use Addons\Editor\Editor;
use Addons\Editor\TinyMCE;
use Addons\Font\Font;
use Addons\Heartbeat\Heartbeat;
use Addons\Lazy_Load\Lazy_Load;
use Addons\Minifier\Minify_Html;
use Addons\reCAPTCHA\reCAPTCHA;
use Addons\SMTP\SMTP;
use Addons\SVG\SVG;

\defined( 'ABSPATH' ) || die;

/**
 * Addons Class
 *
 * @author HD Team
 */
final class Addons {

	public mixed $optimizer_options;

	public function __construct() {
		$this->optimizer_options = get_option( 'optimizer__options', [] );

		add_action( 'plugins_loaded', [ &$this, 'i18n' ], 1 );
		add_action( 'plugins_loaded', [ &$this, 'plugins_loaded' ] );

		add_action( 'admin_enqueue_scripts', [ &$this, 'admin_enqueue_scripts' ], 39 );
		add_action( 'admin_menu', [ &$this, 'admin_menu' ] );

		// Parser functions
		$this->_parser();
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
	public function plugins_loaded(): void {

		( new Editor() );
		( new TinyMCE() );
		( new Custom_Order() );
		( new Custom_Email() );
		( new SMTP() );
		( new SVG() );
		( new Base_Slug() );
		( new Heartbeat() );
		( new Lazy_Load() );
		( new reCAPTCHA() );
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	private function _parser(): void {
		if ( defined( 'WP_CLI' ) || is_admin() ) {
			return;
		}

		$minify_html   = $this->optimizer_options['minify_html'] ?? 0;
		$font_optimize = $this->optimizer_options['font_optimize'] ?? 0;
		$font_preload  = $this->optimizer_options['font_preload'] ? implode( PHP_EOL, $this->optimizer_options['font_preload'] ) : '';
		$dns_prefetch  = $this->optimizer_options['dns_prefetch'] ? implode( PHP_EOL, $this->optimizer_options['dns_prefetch'] ) : '';

		if ( ! empty( $minify_html ) ||
		     ! empty( $font_optimize ) ||
		     ! empty( $font_preload ) ||
		     ! empty( $dns_prefetch )
		) {
			add_action( 'wp_loaded', [ &$this, 'start_bufffer' ] );
			add_action( 'shutdown', [ &$this, 'end_buffer' ] );
		}
	}

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	public function start_bufffer(): void {
		ob_start( [ &$this, 'run' ] );
	}

	/** ----------------------------------------------- */

	/**
	 * @return void
	 */
	public function end_buffer(): void {
		if ( ob_get_length() ) {
			ob_end_flush();
		}
	}

	/** ---------------------------------------- */

	/**
	 * @param string $html
	 *
	 * @return string
	 */
	public function run( string $html ): string {
		if ( ! preg_match( '/<\/html>/i', $html ) ) {
			return $html;
		}

		// Do not run optimization if amp is active, the page is an xml or feed.
		if ( is_amp_enabled( $html ) ||
		     is_xml( $html ) ||
		     is_feed()
		) {
			return $html;
		}

		return $this->optimize_for_visitors( $html );
	}

	/** ----------------------------------------------- */

	/**
	 * @param $html
	 *
	 * @return string
	 */
	public function optimize_for_visitors( $html ): string {

		$html = ( new Font() )->run( $html );
		$html = $this->dns_prefetch( $html );

		$minify_html = $this->optimizer_options['minify_html'] ?? 0;
		if ( ! empty( $minify_html ) ) {
			$html = Minify_Html::minify( $html );
		}

		return $html;
	}

	// ------------------------------------------------------

	/**
	 * @param $html
	 *
	 * @return array|mixed|string|string[]
	 */
	public function dns_prefetch( $html ): mixed {

		// Check if there are any urls inserted by the user.
		$urls = $this->optimizer_options['dns_prefetch'] ?? false;

		// Return, if no url's are set by the user.
		if ( empty( $urls ) ) {
			return $html;
		}

		$new_html = '';

		foreach ( $urls as $url ) {

			// Replace the protocol with //.
			$url_without_protocol = preg_replace( '~(?:(?:https?:)?(?:\/\/)(?:www\.|(?!www)))?((?:.*?)\.(?:.*))~', '//$1', $url );

			$new_html .= '<link rel="dns-prefetch" href="' . $url_without_protocol . '" />';
		}

		return str_replace( '</head>', $new_html . '</head>', $html );
	}

	/** ---------------------------------------- */

	/**
	 * @return void
	 */
	public function admin_enqueue_scripts(): void {
//		if ( ! wp_style_is( 'woocommerce_admin_styles' ) ) { // tested
//			wp_enqueue_style( 'select2-style', ADDONS_URL . 'assets/css/select2.min.css', [], ADDONS_VERSION );
//		}
//
//		if ( ! wp_script_is( 'select2', 'registered' ) ) {
//			wp_register_script( 'select2', ADDONS_URL . 'assets/js/select2.full.min.js', [ 'jquery-core' ], ADDONS_VERSION );
//		}

		wp_enqueue_style( 'addon-style', ADDONS_URL . 'assets/css/addon.css', [], ADDONS_VERSION );
		wp_enqueue_script( 'addon', ADDONS_URL . 'assets/js/addon.js', [ 'jquery-core', 'admin' ], ADDONS_VERSION, true );
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
