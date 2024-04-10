<?php

namespace Cores\Traits;

use Vectorface\Whip\Whip;

\defined( 'ABSPATH' ) || die;

trait Url {

	/**
	 * @param string $img
	 *
	 * @return string
	 */
	public static function pixelImg( string $img = '' ): string {
		if ( file_exists( $img ) ) {
			return $img;
		}

		return "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==";
	}

	/**
	 * @return string
	 */
	public static function getIpAddress(): string {

		if ( class_exists( '\Vectorface\Whip\Whip' ) ) {
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
			} elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
				return $forward;
			} else {
				return $remote;
			}
		}

		// Fallback local ip.
		return '127.0.0.1';
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	public static function urlToPath( $url ): string {
		return substr( get_home_path(), 0, - 1 ) . wp_make_link_relative( $url );
	}

	/**
	 * @param $dir
	 *
	 * @return array|string|string[]
	 */
	public static function pathToUrl( $dir ): array|string {
		$dirs = wp_upload_dir();
		$url  = str_replace( $dirs['basedir'], $dirs['baseurl'], $dir );

		return str_replace( ABSPATH, self::home(), $url );
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	public static function home( string $path = '' ): string {
		return trailingslashit( esc_url( network_home_url( $path ) ) );
	}

	/**
	 * @param bool $nopaging
	 * @param bool $get_vars
	 *
	 * @return string
	 */
	public static function current( bool $nopaging = true, bool $get_vars = true ): string {
		global $wp;

		$current_url =  self::home( $wp->request );

		// get the position where '/page. ' text start.
		$pos = strpos($current_url , '/page');

		// remove string from the specific position
		if ( $nopaging && $pos ) {
			$current_url = trailingslashit( substr( $current_url, 0, $pos ) );
		}

		if ( $get_vars ) {
			$queryString =  http_build_query( $_GET );

			if (strpos( $current_url, "?") && $queryString ) {
				$current_url .= "&" . $queryString;
			}
			elseif ( $queryString ) {
				$current_url .= "?" . $queryString;
			}
		}

		return $current_url;
	}

	/**
	 * Normalize the given path. On Windows servers backslash will be replaced
	 * with slash. Removes unnecessary double slashes and double dots. Removes
	 * last slash if it exists.
	 *
	 * Examples:
	 * path::normalize("C:\\any\\path\\") returns "C:/any/path"
	 * path::normalize("/your/path/..//home/") returns "/your/home"
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	public static function normalizePath( string $path ): string {
		// Backslash to slash convert
		if ( strtoupper( substr( PHP_OS, 0, 3 ) ) == "WIN" ) {
			$path = preg_replace( '/([^\\\])\\\+([^\\\])/s', "$1/$2", $path );
			if ( str_ends_with( $path, "\\" ) ) {
				$path = substr( $path, 0, - 1 );
			}
			if ( str_starts_with( $path, "\\" ) ) {
				$path = "/" . substr( $path, 1 );
			}
		}
		$path = preg_replace( '/\/+/s', "/", $path );
		$path = "/$path";
		if ( ! str_ends_with( $path, "/" ) ) {
			$path .= "/";
		}
		$expr = '/\/([^\/]{1}|[^\.\/]{2}|[^\/]{3,})\/\.\.\//s';
		while ( preg_match( $expr, $path ) ) {
			$path = preg_replace( $expr, "/", $path );
		}
		$path = substr( $path, 0, - 1 );

		return substr( $path, 1 );
	}

	/**
	 * @param string $url
	 *
	 * @return array
	 */
	public static function queries( string $url ): array {
		$queries = [];
		parse_str( wp_parse_url( $url, PHP_URL_QUERY ), $queries );

		return $queries;
	}

	/**
	 * @param string $url
	 * @param $param
	 * @param null $fallback
	 *
	 * @return int|string|null
	 */
	public static function query( string $url, $param, $fallback = null ): int|string|null {
		$queries = self::queries( $url );
		if ( ! isset( $queries[ $param ] ) ) {
			return $fallback;
		}

		return $queries[ $param ];
	}

	/**
	 * @param string $url
	 *
	 * @return int|false
	 */
	public static function remoteStatusCheck( string $url ): false|int {
		$response = wp_safe_remote_head( $url, [
			'timeout'   => 5,
			'sslverify' => false,
		] );
		if ( ! is_wp_error( $response ) ) {
			return $response['response']['code'];
		}

		return false;
	}
}
