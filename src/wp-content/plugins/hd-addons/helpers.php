<?php

use Detection\Exception\MobileDetectException;
use Detection\MobileDetect;

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

if ( ! function_exists( 'filter_setting_options' ) ) {
	/**
	 * @param $name
	 * @param mixed $default
	 *
	 * @return array|mixed
	 */
	function filter_setting_options( $name, mixed $default = [] ): mixed {
		$filters = apply_filters( 'hd_theme_setting_options', [] );

		if ( isset( $filters[ $name ] ) ) {
			return $filters[ $name ] ?: $default;
		}

		return [];
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'message_success' ) ) {
	/**
	 * @param $message
	 * @param bool $auto_hide
	 *
	 * @return void
	 */
	function message_success( $message, bool $auto_hide = false ): void {
		$message = $message ?: 'Values saved';
		$message = __( $message, ADDONS_TEXT_DOMAIN );

		$class = 'notice notice-success is-dismissible';
		if ( $auto_hide ) {
			$class .= ' dismissible-auto';
		}

		printf( '<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', esc_attr_strip_tags( $class ), $message );
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'message_error' ) ) {
	/**
	 * @param $message
	 * @param bool $auto_hide
	 *
	 * @return void
	 */
	function message_error( $message, bool $auto_hide = false ): void {
		$message = $message ?: 'Values error';
		$message = __( $message, ADDONS_TEXT_DOMAIN );

		$class = 'notice notice-error is-dismissible';
		if ( $auto_hide ) {
			$class .= ' dismissible-auto';
		}

		printf( '<div class="%1$s"><p><strong>%2$s</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', esc_attr_strip_tags( $class ), $message );
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

if ( ! function_exists( 'redirect' ) ) {
	/**
	 * @param string $uri
	 * @param int $status
	 *
	 * @return true|void
	 */
	function redirect( string $uri = '', int $status = 301 ) {
		if ( ! preg_match( '#^(\w+:)?//#', $uri ) ) {
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

if ( ! function_exists( 'htaccess' ) ) {
	/**
	 * @return bool
	 */
	function htaccess(): bool {

		// Apache
		if ( function_exists( 'apache_get_modules' ) && in_array( 'mod_rewrite', apache_get_modules(), false ) ) {
			return true;
		}

		// ?
		if ( isset( $_SERVER['HTACCESS'] ) && 'on' === $_SERVER['HTACCESS'] ) {
			return true;
		}

		return false;
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
		} elseif ( str_contains( $_SERVER['HTTP_USER_AGENT'], 'Mobile' ) ||
		           str_contains( $_SERVER['HTTP_USER_AGENT'], 'Android' ) ||
		           str_contains( $_SERVER['HTTP_USER_AGENT'], 'Silk/' ) ||
		           str_contains( $_SERVER['HTTP_USER_AGENT'], 'Kindle' ) ||
		           str_contains( $_SERVER['HTTP_USER_AGENT'], 'BlackBerry' ) ||
		           str_contains( $_SERVER['HTTP_USER_AGENT'], 'Opera Mini' ) ||
		           str_contains( $_SERVER['HTTP_USER_AGENT'], 'Opera Mobi' )
		) {
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
		if ( in_array( $current, $checked_arr, false ) ) {
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
			if ( $remove_empty ) {
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

if ( ! function_exists( 'get_custom_post_option_content' ) ) {
	/**
	 * @param string $post_type - max 20 characters
	 * @param bool $encode
	 *
	 * @return array|string
	 */
	function get_custom_post_option_content( string $post_type, bool $encode = false ): array|string {
		if ( empty( $post_type ) ) {
			return '';
		}

		$post = get_custom_post_option( $post_type );
		if ( isset( $post->post_content ) ) {
			$post_content = wp_unslash( $post->post_content );
			if ( $encode ) {
				$post_content = wp_unslash( base64_decode( $post->post_content ) );
			}

			return $post_content;
		}

		return '';
	}
}

/** ----------------------------------------------- */

if ( ! function_exists( 'get_custom_post_option' ) ) {
	/**
	 * @param string $post_type - max 20 characters
	 *
	 * @return array|WP_Post|null
	 */
	function get_custom_post_option( string $post_type ): array|WP_Post|null {
		if ( empty( $post_type ) ) {
			return null;
		}

		$custom_query_vars = [
			'post_type'              => $post_type,
			'post_status'            => get_post_stati(),
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'cache_results'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'lazy_load_term_meta'    => false,
		];

		$post    = null;
		$post_id = get_theme_mod( $post_type . '_option_id' );

		if ( $post_id > 0 && get_post( $post_id ) ) {
			$post = get_post( $post_id );
		}

		// `-1` indicates no post exists; no query necessary.
		if ( ! $post && - 1 !== $post_id ) {
			$post = ( new \WP_Query( $custom_query_vars ) )->post;

			set_theme_mod( $post_type . '_option_id', $post->ID ?? - 1 );
		}

		return $post;
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

		return array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, false );
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
		$smtp_plugins_support = filter_setting_options( 'smtp_plugins_support', [] );

		$check = true;
		if ( ! empty( $smtp_plugins_support ) ) {
			foreach ( $smtp_plugins_support as $plugin_slug ) {
				if ( check_plugin_active( $plugin_slug ) ) {
					$check = false;
					break;
				}
			}
		}

		return $check;
	}
}