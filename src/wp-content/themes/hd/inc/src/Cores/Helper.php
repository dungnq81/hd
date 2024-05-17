<?php

namespace Cores;

use DirectoryIterator;
use MatthiasMullie\Minify;

use Cores\Traits\WooCommerce;
use Cores\Traits\Wp;

\defined( 'ABSPATH' ) || die;

/**
 * Helper Class
 *
 * @author WEBHD
 */
final class Helper {

	use WooCommerce;
	use Wp;

	// --------------------------------------------------

	/**
	 * Find an attribute and add the data as an HTML string.
	 *
	 * @param string $str The HTML string.
	 * @param string $attr The attribute to find.
	 * @param string $content_extra The content that needs to be appended.
	 * @param bool $unique Do we need to filter for unique values?
	 *
	 * @return string
	 */
	public static function appendToAttribute( string $str, string $attr, string $content_extra, bool $unique = false ): string {

		// Check if attribute has single or double quotes.
		// @codingStandardsIgnoreLine
		if ( $start = stripos( $str, $attr . '="' ) ) {
			$quote = '"';

			// @codingStandardsIgnoreLine
		} elseif ( $start = stripos( $str, $attr . "='" ) ) {
			$quote = "'";

		} else {
			// Not found
			return $str;
		}

		// Add quote (for filtering purposes).
		$attr .= '=' . $quote;

		$content_extra = trim( $content_extra );

		if ( $unique ) {

			$start += strlen( $attr );
			$end   = strpos( $str, $quote, $start );

			// Get the current content.
			$content = explode( ' ', substr( $str, $start, $end - $start ) );

			// Get our extra content.
			foreach ( explode( ' ', $content_extra ) as $class ) {
				if ( ! empty( $class ) && ! in_array( $class, $content, true ) ) {

					// This one can be added!
					$content[] = $class;
				}
			}

			// Remove duplicates and empty values.
			$content = array_unique( array_filter( $content ) );
			$content = implode( ' ', $content );

			$before_content = substr( $str, 0, $start );
			$after_content  = substr( $str, $end );

			// Combine the string again.
			$str = $before_content . $content . $after_content;

		} else {
			$str = preg_replace(
				'/' . preg_quote( $attr, '/' ) . '/',
				$attr . $content_extra . ' ',
				$str,
				1
			);
		}

		return $str;
	}

	// --------------------------------------------------

	/**
	 * @param $css
	 * @param bool $debug_check
	 *
	 * @return string
	 */
	public static function CSS_Minify( $css, bool $debug_check = true ): string {
		if ( empty( $css ) ) {
			return $css;
		}

		if ( true === $debug_check && WP_DEBUG ) {
			return $css;
		}

		if ( class_exists( '\MatthiasMullie\Minify\CSS' ) ) {
			return ( new Minify\CSS() )->add( $css )->minify();
		}

		return $css;
	}

	// --------------------------------------------------

	/**
	 * @param $content
	 * @param $link
	 * @param string $class
	 * @param string $label
	 *
	 * @return string
	 */
	public static function ACF_Link_Wrap( $content, $link, string $class = '', string $label = '' ): string {
		$link_return = '';

		if ( is_string( $link ) ) {
			$link_return = sprintf( '<a class="%3$s" href="%1$s" title="%2$s"', esc_url( trim( $link ) ), esc_attr( $label ), esc_attr( $class ) );
			$link_return .= '>' . $content . '</a>';

			return wp_targeted_link_rel( $link_return );
		}

		$link = (array) $link;
		if ( $link ) {
			$_link_title  = $link['title'] ?? '';
			$_link_url    = $link['url'] ?? '#';
			$_link_target = $link['target'] ?? '';

			$link_return = sprintf( '<a class="%3$s" href="%1$s" title="%2$s"', esc_url( $_link_url ), esc_attr( $_link_title ), esc_attr( $class ) );
			if ( ! empty( $_link_target ) ) {
				$link_return .= ' target="_blank"';
			}

			$link_return .= '>';
			$link_return .= $content;
			$link_return .= '</a>';
			$link_return = wp_targeted_link_rel( $link_return );
		}

		return $link_return;
	}

	// --------------------------------------------------

	/**
	 * @param array|string $link
	 * @param string $class
	 * @param string $label
	 * @param string $extra_title
	 *
	 * @return string
	 */
	public static function ACF_Link( array|string $link, string $class = '', string $label = '', string $extra_title = '' ): string {
		$link_return = '';

		// string
		if ( ! empty( $link ) && is_string( $link ) ) {
			$link_return = sprintf( '<a class="%3$s" href="%1$s" title="%2$s"', esc_url( trim( $link ) ), esc_attr( $label ), esc_attr( $class ) );
			$link_return .= '>';
			$link_return .= $label . $extra_title;
			$link_return .= '</a>';

			return wp_targeted_link_rel( $link_return );
		}

		// array
		if ( ! empty( $link ) && is_array( $link ) ) {
			$_link_title  = $link['title'] ?? '';
			$_link_url    = $link['url'] ?? '#';
			$_link_target = $link['target'] ?? '';

			$link_return = sprintf( '<a class="%3$s" href="%1$s" title="%2$s"', esc_url( $_link_url ), esc_attr( $_link_title ), esc_attr( $class ) );
			if ( ! empty( $_link_target ) ) {
				$link_return .= ' target="_blank"';
			}

			$link_return .= '>';
			$link_return .= $_link_title . $extra_title;
			$link_return .= '</a>';
			$link_return = wp_targeted_link_rel( $link_return );
		}

		return $link_return;
	}

	// --------------------------------------------------

	/**
	 * @param ?string $path - full-path dir
	 * @param bool $required_path
	 * @param bool $required_new
	 * @param string $FQN
	 * @param bool $is_widget
	 *
	 * @return void
	 */
	public static function FQN_Load( ?string $path, bool $required_path = false, bool $required_new = false, string $FQN = '\\', bool $is_widget = false ): void {
		if ( ! empty( $path ) && is_dir( $path ) ) {

			foreach ( new DirectoryIterator( $path ) as $fileInfo ) {
				if ( $fileInfo->isDot() ) {
					continue;
				}

				$filename    = self::fileName( $fileInfo, false );
				$filenameFQN = $FQN . $filename;
				$fileExt     = self::fileExtension( $fileInfo, true ); // true: include dot

				if ( '.php' === strtolower( $fileExt ) ) {

					if ( $required_path ) {
						require $path . DIRECTORY_SEPARATOR . $filename . $fileExt;
					}

					if ( $required_new ) {
						if ( ! $is_widget ) {
							class_exists( $filenameFQN ) && ( new $filenameFQN() );
						} else {
							class_exists( $filenameFQN ) && register_widget( new $filenameFQN() );
						}
					}
				}
			}
		}
	}

	// -------------------------------------------------------------

	/**
	 * @param $post_id
	 * @param $format_value
	 * @param $escape_html
	 *
	 * @return mixed|object
	 * @throws \JsonException
	 */
	public static function acfFields( $post_id = false, $format_value = true, $escape_html = false ): mixed {
		if ( ! self::is_acf_pro_active() && ! self::is_acf_active() ) {
			return (object) [];
		}

		$_fields = \get_fields( $post_id, $format_value, $escape_html ) ?? [];

		return self::toObject( $_fields );
	}

	// -------------------------------------------------------------

	/**
	 * @param       $url
	 * @param int $resolution_key
	 *
	 * @return string
	 */
	public static function youtubeImage( $url, int $resolution_key = 0 ): string {
		if ( ! $url ) {
			return '';
		}

		$resolution = [
			'sddefault',
			'hqdefault',
			'mqdefault',
			'default',
			'maxresdefault',
		];

		$url_img = self::pixelImg();
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $vars );
		if ( isset( $vars['v'] ) ) {
			$id      = $vars['v'];
			$url_img = 'https://img.youtube.com/vi/' . $id . '/' . $resolution[ $resolution_key ] . '.jpg';
		}

		return $url_img;
	}

	// -------------------------------------------------------------

	/**
	 * @param      $url
	 * @param int $autoplay
	 * @param bool $lazyload
	 * @param bool $control
	 *
	 * @return string|null
	 */
	public static function youtubeIframe( $url, int $autoplay = 0, bool $lazyload = true, bool $control = true ): ?string {
		$autoplay = (int) $autoplay;
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $vars );
		$home = trailingslashit( network_home_url() );

		if ( isset( $vars['v'] ) ) {
			$idurl     = $vars['v'];
			$_size     = ' width="800px" height="450px"';
			$_autoplay = 'autoplay=' . $autoplay;
			$_auto     = ' allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"';
			if ( $autoplay ) {
				$_auto = ' allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"';
			}
			$_src     = 'https://www.youtube.com/embed/' . $idurl . '?wmode=transparent&origin=' . $home . '&' . $_autoplay;
			$_control = '';
			if ( ! $control ) {
				$_control = '&modestbranding=1&controls=0&rel=0&version=3&loop=1&enablejsapi=1&iv_load_policy=3&playlist=' . $idurl . '&playerapiid=ytb_iframe_' . $idurl;
			}
			$_src  .= $_control . '&html5=1';
			$_src  = ' src="' . $_src . '"';
			$_lazy = '';
			if ( $lazyload ) {
				$_lazy = ' loading="lazy"';
			}

			return '<iframe id="ytb_iframe_' . $idurl . '" title="YouTube Video Player"' . $_lazy . $_auto . $_size . $_src . ' style="border:0"></iframe>';
		}

		return null;
	}

	// -------------------------------------------------------------

	/**
	 * @param string $uri
	 * @param int $status
	 *
	 * @return true|void
	 */
	public static function redirect( string $uri = '', int $status = 301 ) {
		if ( ! preg_match( '#^(\w+:)?//#', $uri ) ) {
			$uri = self::home( $uri );
		}

		if ( ! headers_sent() ) {
			wp_safe_redirect( $uri, $status );
		} else {
			echo '<script>';
			echo 'window.location.href="' . $uri . '";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url=' . $uri . '" />';
			echo '</noscript>';

			return true;
		}
	}

	// -------------------------------------------------------------

	/**
	 * @param bool $img_wrap
	 * @param bool $thumb
	 *
	 * @return string
	 */
	public static function placeholderSrc( bool $img_wrap = true, bool $thumb = true ): string {
		$src = THEME_URL . 'storage/img/placeholder.png';
		if ( $thumb ) {
			$src = THEME_URL . 'storage/img/placeholder-320x320.png';
		}
		if ( $img_wrap ) {
			$src = "<img loading=\"lazy\" src=\"{$src}\" alt=\"placeholder\" class=\"wp-placeholder\">";
		}

		return $src;
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function is_contact_form_7_active(): bool {
		return self::check_plugin_active( 'woocommerce/woocommerce.php' );
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function is_woocommerce_active(): bool {
		return self::check_plugin_active( 'woocommerce/woocommerce.php' );
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function is_acf_pro_active(): bool {
		return self::check_plugin_active( 'advanced-custom-fields-pro/acf.php' );
	}

	// -------------------------------------------------------------

	/**
	 * @return bool
	 */
	public static function is_acf_active(): bool {
		return self::check_plugin_active( 'advanced-custom-fields/acf.php' );
	}

	// -------------------------------------------------------------

	/**
	 * @param string $folder
	 * @param string $file
	 *
	 * @return bool
	 */
	public static function is_addons_active( string $folder = 'hd-addons', string $file = 'hd-addons.php' ): bool {
		if ( empty( $folder ) ) {
			$folder = 'hd-addons';
		}
		if ( empty( $file ) ) {
			$file = 'hd-addons.php';
		}

		return self::check_plugin_active( $folder . '/' . $file );
	}
}
