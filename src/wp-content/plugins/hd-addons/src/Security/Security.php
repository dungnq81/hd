<?php

namespace Addons\Security;

use Addons\Security\Options\Headers;
use Addons\Security\Options\Illegal_Users;
use Addons\Security\Options\Login_Attempts;
use Addons\Security\Options\Readme;

\defined( 'ABSPATH' ) || die;

/**
 * Security Class
 *
 * @author HD
 */
final class Security {

	/**
	 * @var array|false|mixed
	 */
	public mixed $security_options = [];

	// ------------------------------------------------------

	public function __construct() {
		$this->security_options = get_option( 'security__options', false );

		$this->_illegal_users();
		$this->_hide_wp_version();
		$this->_disable_XMLRPC();
		$this->_disable_Opml();
		$this->_remove_ReadMe();
		$this->_disable_RSSFeed();
		$this->_xss_protection();
		$this->_login_attempts();
	}

	// ------------------------------------------------------

	/**
	 * Add login service hooks.
	 *
	 * @return void
	 */
	private function _login_attempts(): void {
		$limit_login_attempts = $this->security_options['limit_login_attempts'] ?? 0;
		$security_login       = new Login_Attempts();

		// Bail if optimization is disabled.
		if ( 0 === (int) $limit_login_attempts ) {
			$security_login->reset_login_attempts();

			return;
		}

		// Check the login attempts for an IP and block the access to the login page.
		add_action( 'login_head', [ &$security_login, 'maybe_block_login_access' ], PHP_INT_MAX );

		// Add login attempts for ip.
		add_filter( 'login_errors', [ &$security_login, 'log_login_attempt' ] );

		// Reset login attempts for an ip on successful login.
		add_filter( 'wp_login', [ &$security_login, 'reset_login_attempts' ] );
	}

	// ------------------------------------------------------

	/**
	 * Add username hooks.
	 *
	 * @return void
	 */
	private function _illegal_users(): void {
		if ( $this->security_options['illegal_users'] ?? 0 ) {
			$common_user = new Illegal_Users();
			add_action( 'illegal_user_logins', [ &$common_user, 'get_illegal_usernames' ] );
		}
	}

	// ------------------------------------------------------

	/**
	 * Add headers_service hooks.
	 *
	 * @return void
	 */
	private function _xss_protection(): void {

		if ( $this->security_options['advanced_xss_protection'] ?? 0 ) {
			$headers = new Headers();

			// Add security headers.
			add_action( 'wp_headers', [ &$headers, 'set_security_headers' ] );

			// Add security headers for rest.
			add_filter( 'rest_post_dispatch', [ &$headers, 'set_rest_security_headers' ] );
		}
	}

	// ------------------------------------------------------

	/**
	 * Remove the WordPress version meta-tag and parameter.
	 *
	 * @return void
	 */
	private function _hide_wp_version(): void {
		if ( $this->security_options['hide_wp_version'] ?? 0 ) {

			// Remove an admin wp version
			add_filter( 'update_footer', '__return_empty_string', 11 );

			// Remove WP version from RSS.
			add_filter( 'the_generator', '__return_empty_string' );

			add_filter( 'style_loader_src', [ &$this, 'remove_version_scripts_styles' ], PHP_INT_MAX );
			add_filter( 'script_loader_src', [ &$this, 'remove_version_scripts_styles' ], PHP_INT_MAX );
		}
	}

	// ------------------------------------------------------

	/**
	 * Remove a version from scripts and styles
	 *
	 * @param $src
	 *
	 * @return false|mixed|string
	 */
	public function remove_version_scripts_styles( $src ): mixed {
		if ( $src && str_contains( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}

	// ------------------------------------------------------

	/**
	 * Disable the WordPress feed.
	 *
	 * @return void
	 */
	private function _disable_RSSFeed(): void {

		// If the option is already enabled.
		if ( $this->security_options['rss_feed_off'] ?? 0 ) {

			add_action( 'do_feed', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_rdf', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_rss', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_rss2', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_atom', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_rss2_comments', [ &$this, 'disable_feed' ], 1 );
			add_action( 'do_feed_atom_comments', [ &$this, 'disable_feed' ], 1 );

			remove_action( 'wp_head', 'feed_links_extra', 3 ); // remove comments feed.
			remove_action( 'wp_head', 'feed_links', 2 );
		}
	}

	// ------------------------------------------------------

	/**
	 * Disables the WordPress feed.
	 *
	 * @return void
	 */
	public function disable_feed(): void {
		redirect( trailingslashit( esc_url( network_home_url() ) ) );
	}

	// ------------------------------------------------------

	/**
	 * Add readme hooks.
	 *
	 * @return void
	 */
	private function _remove_ReadMe(): void {
		if ( $this->security_options['remove_readme'] ?? 0 ) {

			// Add action to delete the README on WP core update if the option is set.
			$readme = new Readme();
			add_action( '_core_updated_successfully', [ &$readme, 'delete_readme' ] );
		}
	}

	// ------------------------------------------------------

	/**
	 * Opml
	 *
	 * @return void
	 */
	private function _disable_Opml(): void {
		if ( $this->security_options['wp_links_opml_off'] ?? 0 ) {
			add_action( 'init', function () {

				// Check if the request matches wp-links-opml.php
				if ( str_contains( $_SERVER['REQUEST_URI'], 'wp-links-opml.php' ) ) {

					// If matched, send a 403 Forbidden response and exit
					status_header( 403 );
					exit;
				}
			} );
		}
	}

	// ------------------------------------------------------

	/**
	 * XML-RPC
	 *
	 * @return void
	 */
	private function _disable_XMLRPC(): void {
		if ( $this->security_options['xml_rpc_off'] ?? 0 ) {

			// Disable XML-RPC authentication and related functions
			if ( is_admin() ) {
				update_option( 'default_ping_status', 'closed' );
			}

			add_filter( 'xmlrpc_enabled', '__return_false' );
			add_filter( 'pre_update_option_enable_xmlrpc', '__return_false' );
			add_filter( 'pre_option_enable_xmlrpc', '__return_zero' );

			// Unset XML-RPC headers
			add_filter( 'wp_headers', function ( $headers ) {
				if ( isset( $headers['X-Pingback'] ) ) {
					unset( $headers['X-Pingback'] );
				}

				return $headers;
			}, 10, 1 );

			// Unset XML-RPC methods for ping-backs
			add_filter( 'xmlrpc_methods', function ( $methods ) {
				unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );

				return $methods;
			}, 10, 1 );
		}
	}
}
