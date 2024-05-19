<?php

use Detection\Exception\MobileDetectException;
use Detection\MobileDetect;
use Vectorface\Whip\Whip;

defined( 'ABSPATH' ) || die;

/** ----------------------------------------------- */

if ( ! function_exists( 'security_options' ) ) {
	/**
	 * @param $key
	 * @param mixed $default
	 *
	 * @return mixed|string
	 */
	function security_options( $key, mixed $default = '' ): mixed {
		$security_options = get_option( 'security__options', [] );

		return $security_options[ $key ] ?? $default;
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'optimizer_options' ) ) {
	/**
	 * @param $key
	 * @param mixed $default
	 *
	 * @return mixed|string
	 */
	function optimizer_options( $key, mixed $default = '' ): mixed {
		$optimizer_options = get_option( 'optimizer__options', [] );

		return $optimizer_options[ $key ] ?? $default;
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'esc_attr_strip_tags' ) ) {
	/**
	 * @param $string
	 *
	 * @return string|null
	 */
	function esc_attr_strip_tags( $string ): ?string {
		$string = strip_tags( $string );
		$string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
		return esc_attr( $string );
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'ip_address' ) ) {
	/**
	 * Get the IP address from which the user is viewing the current page.
	 *
	 * @return string
	 */
	function ip_address(): string {

		if ( class_exists( Whip::class ) ) {
			$whip          = new Whip( Whip::CLOUDFLARE_HEADERS | Whip::REMOTE_ADDR | Whip::PROXY_HEADERS | Whip::INCAPSULA_HEADERS );
			$clientAddress = $whip->getValidIpAddress();

			if ( false !== $clientAddress ) {
				return preg_replace( '/^::1$/', '127.0.0.1', $clientAddress );
			}

		} else {

			// Get real visitor IP behind CloudFlare network
			if ( isset( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) {
				$_SERVER['REMOTE_ADDR']    = $_SERVER["HTTP_CF_CONNECTING_IP"];
				$_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
			}

			$client  = @$_SERVER['HTTP_CLIENT_IP'];
			$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
			$remote  = $_SERVER['REMOTE_ADDR'];

			if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
				return $client;
			}

			if ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
				return $forward;
			}

			return $remote;
		}

		// Fallback local ip.
		return '127.0.0.1';
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'redirect' ) ) {
	/**
	 * @param string $uri
	 * @param int $status
	 *
	 * @return true|void
	 */
	function redirect( string $uri = '', int $status = 301 ) {
		if ( ! preg_match( '#^(\w+:)?//#i', $uri ) ) {
			$uri = trailingslashit( esc_url( network_home_url( $uri ) ) );
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
}

/** ----------------------------------------------- */

if ( ! function_exists( 'do_lock_write' ) ) {
	/**
	 * Lock file and write something in it.
	 *
	 * @param string $content Content to add.
	 *
	 * @return bool    True on success, false otherwise.
	 */
	function do_lock_write( $path, string $content = '' ): bool {
		$fp = fopen( $path, 'wb+' );

		if ( flock( $fp, LOCK_EX ) ) {
			fwrite( $fp, $content );
			flock( $fp, LOCK_UN );
			fclose( $fp );

			return true;
		}

		fclose( $fp );

		return false;
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'setup_wp_filesystem' ) ) {
	/**
	 * @return mixed
	 */
	function setup_wp_filesystem(): mixed {
		global $wp_filesystem;

		// Initialize the WP filesystem, no more using 'file-put-contents' function.
		// Front-end only. In the back-end; its already included
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'is_mobile' ) ) {
	/**
	 * @return bool
	 * @throws MobileDetectException
	 */
	function is_mobile(): bool {

		if ( class_exists( '\Detection\MobileDetect' ) ) {
			return ( new MobileDetect() )->isMobile();
		}

		if ( function_exists( 'wp_is_mobile' ) ) {
			return wp_is_mobile();
		}

		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$is_mobile = false;
		} elseif ( @strpos( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) !== false
		           || @strpos( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' ) !== false ) {
			$is_mobile = true;
		} else {
			$is_mobile = false;
		}

		return $is_mobile;
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'in_array_checked' ) ) {
	/**
	 * @param array $checked_arr
	 * @param $current
	 * @param bool $display
	 * @param string $type
	 *
	 * @return string
	 */
	function in_array_checked( array $checked_arr, $current, bool $display = true, string $type = 'checked' ): string {
		if ( in_array( $current, $checked_arr, true ) ) {
			$result = " $type='$type'";
		} else {
			$result = '';
		}

		if ( $display ) {
			echo $result;
		}

		return $result;
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'explode_multi' ) ) {
	/**
	 * @param $delimiters
	 * @param $string
	 * @param bool $remove_empty
	 *
	 * @return mixed|string[]
	 */
	function explode_multi( $delimiters, $string, bool $remove_empty = true ): mixed {
		if ( is_string( $delimiters ) ) {
			return explode( $delimiters, $string );
		}

		if ( is_array( $delimiters ) ) {
			$ready  = str_replace( $delimiters, $delimiters[0], $string );
			$launch = explode( $delimiters[0], $ready );
			if ( true === $remove_empty ) {
				$launch = array_filter( $launch );
			}

			return $launch;
		}

		return $string;
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'is_xml' ) ) {
	/**
	 * @param $content
	 *
	 * @return false|int
	 */
	function is_xml( $content ): false|int {

		// Get the first 200 chars of the file to make the preg_match check faster.
		$xml_part = substr( $content, 0, 20 );

		return preg_match( '/<\?xml version="/', $xml_part );
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'is_amp_enabled' ) ) {
	/**
	 * @param $html
	 *
	 * @return false|int
	 */
	function is_amp_enabled( $html ): false|int {

		// Get the first 200 chars of the file to make the preg_match check faster.
		$is_amp = substr( $html, 0, 200 );

		// Checks if the document is containing the amp tag.
		return preg_match( '/<html[^>]+(amp|⚡)[^>]*>/u', $is_amp );
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'get_current_url' ) ) {
	/**
	 * Get the current url.
	 *
	 * @return string The current url.
	 */
	function get_current_url(): string {

		// Return empty string if it is not an HTTP request.
		if ( ! isset( $_SERVER['HTTP_HOST'] ) ) {
			return '';
		}

		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https' : 'http';

		// Build the current url.
		return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'check_plugin_installed' ) ) {
	/**
	 * Check if plugin is installed by getting all plugins from the plugins dir
	 *
	 * @param $plugin_slug
	 *
	 * @return bool
	 */
	function check_plugin_installed( $plugin_slug ): bool {

		// Check if needed functions exist - if not, require them
		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed_plugins = get_plugins();

		return array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, true );
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'check_plugin_active' ) ) {
	/**
	 * Check if the plugin is installed
	 *
	 * @param $plugin_slug
	 *
	 * @return bool
	 */
	function check_plugin_active( $plugin_slug ): bool {
		return check_plugin_installed( $plugin_slug ) && is_plugin_active( $plugin_slug );
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'check_smtp_plugin_active' ) ) {
	/**
	 * @return bool
	 */
	function check_smtp_plugin_active(): bool {
		$hd_smtp_plugins_support = apply_filters( 'hd_smtp_plugins_support', [] );

		$check = true;
		if ( ! empty( $hd_smtp_plugins_support ) ) {
			foreach ( $hd_smtp_plugins_support as $plugin_slug ) {
				if ( check_plugin_active( $plugin_slug ) ) {
					$check = false;
					break;
				}
			}
		}

		return $check;
	}
}