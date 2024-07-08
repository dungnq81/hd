<?php
/**
 * CSS Output functions
 *
 * @author   WEBHD
 */

use Cores\Helper;
use Libs\CSS;

\defined( 'ABSPATH' ) || die;

// ------------------------------------------

/** inline css */
if ( ! function_exists( '__enqueue_inline_css' ) ) {
	add_action( 'wp_enqueue_scripts', '__enqueue_inline_css', 99 );

	/**
	 * Add CSS for third-party plugins.
	 *
	 * @return void
	 */
	function __enqueue_inline_css(): void {
		$css = CSS::get_instance();

		// footer
		$footer_bgcolor = Helper::getThemeMod( 'footer_bgcolor_setting' );
		$footer_color   = Helper::getThemeMod( 'footer_color_setting' );
		$footer_bg      = Helper::getThemeMod( 'footer_bg_setting' );

		if ( $footer_bg ) {
			$css->set_selector( '#footer-widgets::before' );
			$css->add_property( 'background-image', 'url(' . $footer_bg . ')' );
		}

		if ( $footer_color ) {
			$css->set_selector( '#footer-widgets' );
			$css->add_property( 'color', $footer_color );
		}

		if ( $footer_bgcolor ) {
			$css->set_selector( '#footer-widgets' );
			$css->add_property( 'background-color', $footer_bgcolor );
		}

		// header
		$header_bgcolor = Helper::getThemeMod( 'header_bgcolor_setting' );
		$header_bg      = Helper::getThemeMod( 'header_bg_setting' );

		if ( $header_bg ) {
			$css->set_selector( '#masthead::before' );
			$css->add_property( 'background-image', 'url(' . $header_bg . ')' );
		}

		if ( $header_bgcolor ) {
			$css->set_selector( '#masthead' );
			$css->add_property( 'background-color', $header_bgcolor );
		}

		// breadcrumb
		$breadcrumb_max_height = Helper::getThemeMod( 'breadcrumb_max_height_setting' );
		if ( $breadcrumb_max_height ) {
			$css->set_selector( '.section.section-title' );
			$css->add_property( 'height', (int) $breadcrumb_max_height . 'px' );
			$css->add_property( 'overflow', 'hidden' );
			$css->add_property( 'position', 'relative' );
		}

		// output
		if ( $css->css_output() ) {
			wp_add_inline_style( 'app-style', $css->css_output() );
		}
	}
}
