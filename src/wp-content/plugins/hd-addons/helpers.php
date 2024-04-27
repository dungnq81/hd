<?php

\defined( 'ABSPATH' ) || die;

/** ----------------------------------------------- */

if ( ! function_exists( 'is_mobile' ) ) {
	/**
	 * @return bool
	 */
	function is_mobile(): bool {
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
		if ( in_array( $current, $checked_arr ) ) {
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
		return preg_match( '/<html[^>]+(amp|⚡)[^>]*>/', $is_amp );
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
		if ( check_plugin_installed( $plugin_slug ) ) {
			if ( is_plugin_active( $plugin_slug ) ) {
				return true;
			}
		}

		return false;
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
			foreach ( $hd_smtp_plugins_support as $key => $plugin_slug ) {
				if ( check_plugin_active( $plugin_slug ) ) {
					$check = false;
					break;
				}
			}
		}

		return $check;
	}
}