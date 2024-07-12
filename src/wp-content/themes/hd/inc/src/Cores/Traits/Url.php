<?php

namespace Cores\Traits;

use Vectorface\Whip\Whip;

\defined( 'ABSPATH' ) || die;

trait Url {

	// --------------------------------------------------

	/**
	 * @param $url
	 *
	 * @return bool
	 */
	public static function isUrl( $url ): bool {

		// Basic URL validation using filter_var
		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			return false;
		}

		// Parse the URL into components
		$parsed_url = parse_url( $url );

		// Validate scheme
		$valid_schemes = [ 'http', 'https' ];
		if ( ! isset( $parsed_url['scheme'] ) || ! in_array( $parsed_url['scheme'], $valid_schemes, false ) ) {
			return false;
		}

		// Validate host
		if ( ! isset( $parsed_url['host'] ) || ! filter_var( $parsed_url['host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) ) {
			return false;
		}

		// Validate DNS resolution for the host
		if ( ! checkdnsrr( $parsed_url['host'], 'A' ) && ! checkdnsrr( $parsed_url['host'], 'AAAA' ) ) {
			return false;
		}

		return true;
	}

	// --------------------------------------------------

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

	// --------------------------------------------------

	/**
	 * @param $ip
	 * @param $range
	 *
	 * @return bool
	 */
	public static function ipInRange( $ip, $range ): bool {
		if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return false;
		}

		$ipPattern    = '/^(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})$/';
		$rangePattern = '/^(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})-(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])$/';
		$cidrPattern  = '/^(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\/(\d|[1-2]\d|3[0-2])$/';

		// Check if it's a single IP address
		if ( preg_match( $ipPattern, $range ) ) {
			return (string) $ip === (string) $range;
		}

		// Check if it's an IP range
		if ( preg_match( $rangePattern, $range, $matches ) ) {
			$startIP = "{$matches[1]}.{$matches[2]}.{$matches[3]}.{$matches[4]}";
			$endIP   = "{$matches[1]}.{$matches[2]}.{$matches[3]}.{$matches[5]}";

			return self::_compareIPs( $startIP, $endIP ) < 0 && self::_compareIPs( $startIP, $ip ) <= 0 && self::_compareIPs( $ip, $endIP ) <= 0;
		}

		// Check if it's a CIDR notation
		if ( preg_match( $cidrPattern, $range ) ) {
			[ $subnet, $maskLength ] = explode( '/', $range );

			return self::_ipCIDRCheck( $ip, $subnet, $maskLength );
		}

		return false;
	}

	// --------------------------------------------------

	/**
	 * @param $ip
	 * @param $subnet
	 * @param $maskLength
	 *
	 * @return bool
	 */
	public static function _ipCIDRCheck( $ip, $subnet, $maskLength ): bool {
		$ip     = ip2long( $ip );
		$subnet = ip2long( $subnet );
		$mask   = - 1 << ( 32 - $maskLength );
		$subnet &= $mask; // Align the subnet to the mask

		return ( $ip & $mask ) === $subnet;
	}

	// --------------------------------------------------

	/**
	 * @param $range
	 *
	 * @return bool
	 */
	public static function isValidIPRange( $range ): bool {
		$ipPattern    = '/^(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})$/';
		$rangePattern = '/^(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})-(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])$/';
		$cidrPattern  = '/^(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\.(25[0-5]|2[0-4]\d|1\d{2}|\d{1,2})\/(\d|[1-2]\d|3[0-2])$/';

		if ( preg_match( $ipPattern, $range ) ) {
			return true;
		}

		if ( preg_match( $rangePattern, $range, $matches ) ) {
			$startIP = "{$matches[1]}.{$matches[2]}.{$matches[3]}.{$matches[4]}";
			$endIP   = "{$matches[1]}.{$matches[2]}.{$matches[3]}.{$matches[5]}";

			return self::_compareIPs( $startIP, $endIP ) < 0;
		}

		if ( preg_match( $cidrPattern, $range ) ) {
			return true; // Just return true for CIDR notation
		}

		return false;
	}

	// --------------------------------------------------

	/**
	 * @param $ip1
	 * @param $ip2
	 *
	 * @return int
	 */
	public static function _compareIPs( $ip1, $ip2 ): int {
		$ip1Long = (int) ip2long( $ip1 );
		$ip2Long = (int) ip2long( $ip2 );

		if ( $ip1Long < $ip2Long ) {
			return - 1;
		}

		if ( $ip1Long > $ip2Long ) {
			return 1;
		}

		return 0;
	}

	// --------------------------------------------------

	/**
	 * Get the IP address from which the user is viewing the current page.
	 *
	 * @return string
	 */
	public static function getIpAddress(): string {
		if ( class_exists( 'Whip' ) ) {
			$whip          = new Whip( Whip::CLOUDFLARE_HEADERS | Whip::REMOTE_ADDR | Whip::PROXY_HEADERS | Whip::INCAPSULA_HEADERS );
			$clientAddress = $whip->getValidIpAddress();

			if ( false !== $clientAddress ) {
				return preg_replace( '/^::1$/', '127.0.0.1', $clientAddress );
			}
		} else {
			// Check CloudFlare headers
			if ( isset( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) {
				return $_SERVER["HTTP_CF_CONNECTING_IP"];
			}

			// Check forwarded IP (proxy)
			if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && filter_var( $_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP ) ) {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}

			// Check client IP
			if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) && filter_var( $_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP ) ) {
				return $_SERVER['HTTP_CLIENT_IP'];
			}

			// Fallback to remote address
			if ( isset( $_SERVER['REMOTE_ADDR'] ) && filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP ) ) {
				return $_SERVER['REMOTE_ADDR'];
			}
		}

		// Fallback to localhost IP
		return '127.0.0.1';
	}

	// --------------------------------------------------

	/**
	 * @param $url
	 *
	 * @return string
	 */
	public static function urlToPath( $url ): string {
		return substr( get_home_path(), 0, - 1 ) . wp_make_link_relative( $url );
	}

	// --------------------------------------------------

	/**
	 * @param $dir
	 *
	 * @return array|string|string[]
	 */
	public static function pathToUrl( $dir ): array|string {
		$dirs = wp_upload_dir();

		return str_replace( [ $dirs['basedir'], ABSPATH ], [ $dirs['baseurl'], self::home() ], $dir );
	}

	// --------------------------------------------------

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	public static function home( string $path = '' ): string {
		return trailingslashit( esc_url( network_home_url( $path ) ) );
	}

	// --------------------------------------------------

	/**
	 * @param string $path
	 *
	 * @return string|null
	 */
	public static function admin_current_url( string $path = 'admin.php' ): ?string {
		$parsed_url  = parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		$current_url = admin_url( $path );
		if ( $parsed_url ) {
			$current_url .= '?' . $parsed_url['query'];
		}

		return $current_url;
	}

	// --------------------------------------------------

	/**
	 * @param bool $nopaging
	 * @param bool $get_vars
	 *
	 * @return string
	 */
	public static function current( bool $nopaging = true, bool $get_vars = true ): string {
		global $wp;

		$current_url = self::home( $wp->request );

		// get the position where '/page. ' text start.
		$pos = strpos( $current_url, '/page' );

		// remove string from the specific position
		if ( $nopaging && $pos ) {
			$current_url = trailingslashit( substr( $current_url, 0, $pos ) );
		}

		if ( $get_vars ) {
			$queryString = http_build_query( $_GET );

			if ( $queryString && mb_strpos( $current_url, "?" ) ) {
				$current_url .= "&" . $queryString;
			} elseif ( $queryString ) {
				$current_url .= "?" . $queryString;
			}
		}

		return $current_url;
	}

	// --------------------------------------------------

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
		$parts = explode( '/', $path );
		$stack = [];

		foreach ( $parts as $part ) {
			if ( $part === '' || $part === '.' ) {
				// Ignore empty parts and current directory parts (.)
				continue;
			}
			if ( $part === '..' ) {
				// Pop from stack if part is '..' and stack is not empty
				if ( ! empty( $stack ) ) {
					array_pop( $stack );
				}
			} else {
				// Add the part to the stack
				$stack[] = $part;
			}
		}

		// Rebuild the path
		return '/' . implode( '/', $stack );
	}

	// --------------------------------------------------

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

	// --------------------------------------------------

	/**
	 * @param string $url
	 * @param $param
	 * @param null $fallback
	 *
	 * @return int|string|null
	 */
	public static function query( string $url, $param, $fallback = null ): int|string|null {
		$queries = self::queries( $url );

		return $queries[ $param ] ?? $fallback;
	}

	// --------------------------------------------------

	/**
	 * @param string $url
	 *
	 * @return int|false
	 */
	public static function remoteStatusCheck( string $url ): false|int {
		$response = wp_safe_remote_head( $url, [
			'timeout'   => 5,
			'silverier' => false,
		] );

		if ( ! is_wp_error( $response ) ) {
			return $response['response']['code'];
		}

		return false;
	}
}
