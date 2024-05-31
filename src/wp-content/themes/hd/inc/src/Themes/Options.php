<?php

namespace Themes;

use Cores\Helper;

\defined( 'ABSPATH' ) || die;

/**
 * Options Class
 *
 * @author HD
 */
final class Options {

	// --------------------------------------------------

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ &$this, 'aspect_ratio_enqueue_scripts' ], 98 );

		/** Comments */
		//add_action( 'comment_form_after_fields', [ &$this, 'add_simple_antispam_field' ] );
		//add_filter( 'preprocess_comment', [ &$this, 'check_simple_antispam' ] );

		/** Custom Scripts */
		add_action( 'wp_head', [ &$this, 'header_scripts__hook' ], 99 ); // header scripts
		add_action( 'wp_body_open', [ &$this, 'body_scripts_top__hook' ], 99 ); // body scripts - TOP

		add_action( 'wp_footer', [ &$this, 'footer_scripts__hook' ], 1 ); // footer scripts
		add_action( 'wp_footer', [ &$this, 'body_scripts_bottom__hook' ], 998 ); // body scripts - BOTTOM

		/** Custom CSS */
		// add_action('wp_enqueue_scripts', [ &$this, 'header_custom_css' ], 99);
		add_action( 'wp_head', [ &$this, 'header_custom_css' ], 98 );
	}

	// ------------------------------------------------------

	/**
	 * @param $commentdata
	 *
	 * @return mixed
	 */
//	public function check_simple_antispam( $commentdata ): mixed {
//		if ( ! isset( $_POST['antispam_input'], $_POST['antispam_result'] ) ) {
//			wp_die( esc_html__( 'Lỗi CAPTCHA. Vui lòng thử lại.', TEXT_DOMAIN ) );
//		}
//
//		$input  = (int) $_POST['antispam_input'];
//		$result = (int) $_POST['antispam_result'];
//
//		if ( $input !== $result ) {
//			wp_die( esc_html__( 'Câu trả lời chưa chính xác. Vui lòng thử lại.', TEXT_DOMAIN ) );
//		}
//
//		return $commentdata;
//	}

	// ------------------------------------------------------

	/**
	 * @return void
	 * @throws \Random\RandomException
	 */
//	public function add_simple_antispam_field(): void {
//		$comment_options = Helper::getOption( 'comment__options', false, false );
//
//		if ( $comment_options['simple_antispam'] ?? '' ) {
//
//			$num1     = random_int( 1, 10 );
//			$num2     = random_int( 1, 10 );
//			$operator = random_int( 0, 1 ) ? '+' : '-';
//			$result   = $operator === '+' ? $num1 + $num2 : $num1 - $num2;
//
//			echo '<p class="comment-form-antispam">' . sprintf( esc_html__( 'Để xác minh bạn không phải là robot spam comment, Hãy tính: %1$d %2$s %3$d = ?', TEXT_DOMAIN ), $num1, $operator, $num2 ) . '</p>';
//			echo '<input type="hidden" name="antispam_result" value="' . $result . '" />';
//			echo '<p class="comment-form-antispam-answer"><label for="antispam_input">' . esc_html__( 'Câu trả lời:', TEXT_DOMAIN ) . '</label> <input type="text" name="antispam_input" id="antispam_input" required /></p>';
//		}
//	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function header_custom_css(): void {
		/** Custom CSS */
		$css = Helper::getCustomPostContent( 'hd_css', false );

		if ( $css ) {
			$css = Helper::CSS_Minify( $css, true );

			echo "<style id='custom-style-inline-css'>" . $css . "</style>";
			// wp_add_inline_style( 'app-style', $css );
		}
	}

	// ------------------------------------------------------

	/**
	 * @return void
	 */
	public function aspect_ratio_enqueue_scripts(): void {
		$classes = [];
		$styles  = '';

		$aspect_ratio_post_type = Helper::filter_setting_options( 'aspect_ratio_post_type', [] );
		foreach ( $aspect_ratio_post_type as $ar_post_type ) {

			$ratio_obj   = Helper::getAspectRatio( $ar_post_type );
			$ratio_class = $ratio_obj->class ?? '';
			$ratio_style = $ratio_obj->style ?? '';

			if ( $ratio_style && ! in_array( $ratio_class, $classes, false )  ) {
				$classes[] = $ratio_class;
				$styles    .= $ratio_style;
			}
		}

		if ( $styles ) {
			wp_add_inline_style( 'app-style', $styles );
		}
	}

	// ------------------------------------------------------

	/**
	 * Header scripts
	 *
	 * @return void
	 */
	public function header_scripts__hook(): void {
		$html_header = Helper::getCustomPostContent( 'html_header', true );
		if ( $html_header ) {
			echo $html_header;
		}
	}

	/**
	 * Body scripts - TOP
	 *
	 * @return void
	 */
	public function body_scripts_top__hook(): void {
		$html_body_top = Helper::getCustomPostContent( 'html_body_top', true );
		if ( $html_body_top ) {
			echo $html_body_top;
		}
	}

	/**
	 * Footer scripts
	 *
	 * @return void
	 */
	public function footer_scripts__hook(): void {
		$html_footer = Helper::getCustomPostContent( 'html_footer', true );
		if ( $html_footer ) {
			echo $html_footer;
		}
	}

	/**
	 * Body scripts - BOTTOM
	 *
	 * @return void
	 */
	public function body_scripts_bottom__hook(): void {
		$html_body_bottom = Helper::getCustomPostContent( 'html_body_bottom', true );
		if ( $html_body_bottom ) {
			echo $html_body_bottom;
		}
	}
}
