<?php

namespace Cores\Traits;

\defined( 'ABSPATH' ) || die;

trait File {

	// --------------------------------------------------

	/**
	 * Check if the passed content is xml.
	 *
	 * @param string $content The page content.
	 *
	 * @return bool
	 */
	public static function is_xml( string $content ): bool {
		// Get the first 200 chars of the file to make the preg_match check faster.
		$xml_part = substr( $content, 0, 20 );

		return preg_match( '/<\?xml version="/', $xml_part );
	}

	// --------------------------------------------------

	/**
	 * @return bool
	 */
	public static function htAccess(): bool {

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

	// --------------------------------------------------

	/**
	 * @return mixed
	 */
	public static function wpFileSystem(): mixed {
		global $wp_filesystem;

		// Initialize the WP filesystem, no more using 'file-put-contents' function.
		// Front-end only. In the back-end; its already included
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}

	// --------------------------------------------------

	/**
	 * Checks if the current request is a WP REST API request.
	 *
	 * Case #1: After WP_REST_Request initialization
	 * Case #2: Support "plain" permalink settings
	 * Case #3: URL Path begins with wp-json/ (your REST prefix). Also supports WP installations in sub-folders
	 *
	 * @return bool True if it's a rest request, false otherwise.
	 */
	public static function isRest(): bool {
		$prefix = rest_get_url_prefix();
		if (
			( defined( 'REST_REQUEST' ) && \REST_REQUEST ) ||
			(
				isset( $_GET['rest_route'] ) &&
				0 === @strpos( trim( $_GET['rest_route'], '\\/' ), $prefix, 0 )
			)
		) {
			return true;
		}

		$rest_url    = wp_parse_url( site_url( $prefix ) );
		$current_url = wp_parse_url( add_query_arg( [] ) );

		return 0 === @strpos( $current_url['path'], $rest_url['path'], 0 );
	}

	// --------------------------------------------------

	/**
	 * @param $path
	 *
	 * @return true
	 */
	public static function fileCreate( $path ): bool {
		// Setup wp_filesystem.
		$wp_filesystem = self::wpFileSystem();

		// Bail if the file already exists.
		if ( $wp_filesystem->is_file( $path ) ) {
			return true;
		}

		// Create the file.
		return $wp_filesystem->touch( $path );
	}

	// --------------------------------------------------

	/**
	 * Reads entire file into a string
	 *
	 * @param string $file Name of the file to read.
	 *
	 * @return false|string|null Read data on success, false on failure.
	 */
	public static function fileRead( string $file ): false|string|null {
		// Setup wp_filesystem.
		$wp_filesystem = self::wpFileSystem();

		// Bail if we are unable to create the file.
		if ( ! self::fileCreate( $file ) ) {
			return null;
		}

		// Read file
		return $wp_filesystem->get_contents( $file );
	}

	// --------------------------------------------------

	/**
	 * Update a file
	 *
	 * @param string $path Full path to the file
	 * @param string $content File content
	 */
	public static function fileUpdate( string $path, string $content = '' ): void {
		// Setup wp_filesystem.
		$wp_filesystem = self::wpFileSystem();

		// Bail if we are unable to create the file.
		if ( ! self::fileCreate( $path ) ) {
			return;
		}

		// Add the new content into the file.
		$wp_filesystem->put_contents( $path, $content );
	}

	// --------------------------------------------------

	/**
	 * Lock file and write something in it.
	 *
	 * @param string $content Content to add.
	 *
	 * @return bool    True on success, false otherwise.
	 */
	public static function doLockWrite( $path, string $content = '' ): bool {
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

	// --------------------------------------------------

	/**
	 * @param      $filename
	 * @param bool $include_dot
	 *
	 * @return string
	 */
	public static function fileExtension( $filename, bool $include_dot = false ): string {
		$dot = '';
		if ( $include_dot ) {
			$dot = '.';
		}

		return $dot . strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	}

	// --------------------------------------------------

	/**
	 * @param      $filename
	 * @param bool $include_ext
	 *
	 * @return string
	 */
	public static function fileName( $filename, bool $include_ext = false ): string {
		return $include_ext
			? pathinfo( $filename, PATHINFO_FILENAME ) . self::fileExtension( $filename, true )
			: pathinfo( $filename, PATHINFO_FILENAME );
	}

	// --------------------------------------------------

	/**
	 * @param $dirname
	 *
	 * @return bool
	 */
	public static function isEmptyDir( $dirname ): bool {
		if ( ! is_dir( $dirname ) ) {
			return false;
		}

		foreach ( scandir( $dirname, SCANDIR_SORT_NONE ) as $file ) {
			if ( ! in_array( $file, [ '.', '..', '.svn', '.git' ] ) ) {
				return false;
			}
		}

		return true;
	}

	// --------------------------------------------------

	/**
	 * @param $directory
	 *
	 * @return bool
	 */
	public static function createDirectory( $directory ): bool {

		// Create the directory and return the result.
		$is_directory_created = wp_mkdir_p( $directory );

		// Bail if you cannot create temp dir.
		if ( ! $is_directory_created ) {
			// translators: `$directory` is the name of directory that should be created.
			error_log( sprintf( 'Cannot create directory: %s.', $directory ) );
		}

		return $is_directory_created;
	}
}
