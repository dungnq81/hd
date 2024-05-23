<?php

namespace Cores\Traits;

use DateTimeInterface;
use Detection\Exception\MobileDetectException;
use Detection\MobileDetect;
use Exception;

\defined( 'ABSPATH' ) || die;

trait Base {

	// --------------------------------------------------

	/**
	 * @param string $datetime
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function ATOM_format( string $datetime = 'now' ): string {
		return ( new \DateTime( $datetime ) )->format( DateTimeInterface::ATOM );
	}

	// --------------------------------------------------

	/**
	 * @param string $date_time_1
	 * @param string $date_time_2
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function isoDuration( string $date_time_1, string $date_time_2 ): string {

		$_date_time_1 = new \DateTime( $date_time_1 );
		$_date_time_2 = new \DateTime( $date_time_2 );

		$interval = $_date_time_1->diff( $_date_time_2 );

		$isoDuration = 'P';
		$isoDuration .= ( $interval->y > 0 ) ? $interval->y . 'Y' : '';
		$isoDuration .= ( $interval->m > 0 ) ? $interval->m . 'M' : '';
		$isoDuration .= ( $interval->d > 0 ) ? $interval->d . 'D' : '';
		$isoDuration .= 'T';
		$isoDuration .= ( $interval->h > 0 ) ? $interval->h . 'H' : '';
		$isoDuration .= ( $interval->i > 0 ) ? $interval->i . 'M' : '';
		$isoDuration .= ( $interval->s > 0 ) ? $interval->s . 'S' : '';

		return $isoDuration;
	}

	// --------------------------------------------------

	/**
	 * Test if the current browser runs on a mobile device (smartphone, tablet, etc.)
	 *
	 * @return boolean
	 * @throws MobileDetectException
	 */
	public static function is_mobile(): bool {

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

	// --------------------------------------------------

	/**
	 * @param string $version
	 *
	 * @return  bool
	 */
	public static function isPhp( string $version = '5.0.0' ): bool {
		static $phpVer;
		if ( ! isset( $phpVer[ $version ] ) ) {
			$phpVer[ $version ] = ! ( ( version_compare( PHP_VERSION, $version ) < 0 ) );
		}

		return $phpVer[ $version ];
	}

	// --------------------------------------------------

	/**
	 * @param $input
	 *
	 * @return bool
	 */
	public static function isInteger( $input ): bool {
		return ( ctype_digit( (string) $input ) );
	}

	// --------------------------------------------------

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public static function runClosure( $value ): mixed {
		if ( $value instanceof \Closure || ( is_array( $value ) && is_callable( $value ) ) ) {
			return $value();
		}

		return $value;
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 * @param mixed $fallback
	 * @param bool $strict
	 *
	 * @return mixed
	 */
	public static function ifEmpty( $value, $fallback, bool $strict = false ): mixed {
		$isEmpty = $strict ? empty( $value ) : self::isEmpty( $value );

		return $isEmpty ? $fallback : $value;
	}

	// --------------------------------------------------

	/**
	 * @param mixed $condition
	 * @param mixed $ifTrue
	 * @param mixed $ifFalse
	 *
	 * @return mixed
	 */
	public static function ifTrue( $condition, $ifTrue, $ifFalse = null ): mixed {
		return $condition ? self::runClosure( $ifTrue ) : self::runClosure( $ifFalse );
	}

	// --------------------------------------------------

	/**
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public static function isEmpty( $value ): bool {
		if ( is_string( $value ) ) {
			return trim( $value ) === '';
		}

		return ! is_numeric( $value ) && ! is_bool( $value ) && empty( $value );
	}

	// --------------------------------------------------

	/**
	 * @param $array
	 *
	 * @return array
	 */
	public static function removeEmptyValues( $array ): array {

		if ( ! is_array( $array ) && $array ) {
			return [ $array ];
		}

		if ( empty( $array ) ) {
			return [];
		}

		$result = [];
		foreach ( $array as $key => $value ) {
			if ( self::isEmpty( $value ) ) {
				continue;
			}

			$result[ $key ] = self::ifTrue( ! is_array( $value ), $value, function () use ( $value ) {
				return self::removeEmptyValues( $value );
			} );
		}

		return $result;
	}

	// --------------------------------------------------

	/**
	 * @param $value
	 * @param $min
	 * @param $max
	 *
	 * @return bool
	 */
	public static function inRange( $value, $min, $max ): bool {
		$inRange = filter_var( $value, FILTER_VALIDATE_INT, [
			'options' => [
				'min_range' => (int) $min,
				'max_range' => (int) $max,
			],
		] );

		return false !== $inRange;
	}

	// --------------------------------------------------

	/**
	 * Encoded Mailto Link
	 *
	 * Create a spam-protected mailto link written in Javascript
	 *
	 * @param string $email the email address
	 * @param string $title the link title
	 * @param array|string $attributes
	 *
	 * @return string|null
	 */
	public static function safeMailTo( string $email, string $title = '', array|string $attributes = '' ): ?string {
		if ( ! $email || ! is_email( $email ) ) {
			return null;
		}

		if ( trim( $title ) === '' ) {
			$title = $email;
		}

		$x = str_split( '<a href="mailto:', 1 );

		for ( $i = 0, $l = strlen( $email ); $i < $l; $i ++ ) {
			$x[] = '|' . ord( $email[ $i ] );
		}

		$x[] = '"';

		if ( $attributes !== '' ) {
			if ( is_array( $attributes ) ) {
				foreach ( $attributes as $key => $val ) {
					$x[] = ' ' . $key . '="';
					for ( $i = 0, $l = strlen( $val ); $i < $l; $i ++ ) {
						$x[] = '|' . ord( $val[ $i ] );
					}
					$x[] = '"';
				}
			} else {
				for ( $i = 0, $l = mb_strlen( $attributes ); $i < $l; $i ++ ) {
					$x[] = mb_substr( $attributes, $i, 1 );
				}
			}
		}

		$x[] = '>';

		$temp = [];
		for ( $i = 0, $l = strlen( $title ); $i < $l; $i ++ ) {
			$ordinal = ord( $title[ $i ] );

			if ( $ordinal < 128 ) {
				$x[] = '|' . $ordinal;
			} else {
				if ( empty( $temp ) ) {
					$count = ( $ordinal < 224 ) ? 2 : 3;
				}

				$temp[] = $ordinal;
				if ( count( $temp ) === $count ) // @phpstan-ignore-line
				{
					$number = ( $count === 3 ) ? ( ( $temp[0] % 16 ) * 4096 ) + ( ( $temp[1] % 64 ) * 64 ) + ( $temp[2] % 64 ) : ( ( $temp[0] % 32 ) * 64 ) + ( $temp[1] % 64 );
					$x[]    = '|' . $number;
					$count  = 1;
					$temp   = [];
				}
			}
		}

		$x[] = '<';
		$x[] = '/';
		$x[] = 'a';
		$x[] = '>';

		$x = array_reverse( $x );

		// improve obfuscation by eliminating newlines & whitespace
		$output = '<script>'
		          . 'let l=[];';

		foreach ( $x as $i => $value ) {
			$output .= 'l[' . $i . "] = '" . $value . "';";
		}

		return $output . ( 'for (var i = l.length-1; i >= 0; i=i-1) {'
		                   . "if (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");"
		                   . 'else document.write(unescape(l[i]));'
		                   . '}'
		                   . '</script>' );
	}
}
