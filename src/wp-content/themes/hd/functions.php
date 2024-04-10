<?php

/**
 * Theme functions and definitions
 *
 * @package HD
 */

use Cores\Helper;
use Themes\Theme;

$theme_version = ( wp_get_theme()->get( 'Version' ) ) ?: false;
$theme_author  = ( wp_get_theme()->get( 'Author' ) ) ?: 'HD Team';
$theme_uri     = ( wp_get_theme()->get( 'ThemeURI' ) ) ?: 'https://webhd.vn';
$text_domain   = ( wp_get_theme()->get( 'TextDomain' ) ) ?: 'hd';

define( 'HD_TEXT_DOMAIN', $text_domain );
define( 'HD_THEME_VERSION', $theme_version );
define( 'HD_THEME_URI', $theme_uri );
define( 'HD_AUTHOR', $theme_author );

define( 'HD_THEME_PATH', untrailingslashit( get_template_directory() ) . DIRECTORY_SEPARATOR ); // **/wp-content/themes/**/
define( 'HD_THEME_URL', untrailingslashit( esc_url( get_template_directory_uri() ) ) . '/' ); // https://**/wp-content/themes/**/

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	wp_die( __( 'Error locating autoloader. Please run <code>composer install</code>.', HD_TEXT_DOMAIN ) );
}

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/themes.php';
require_once __DIR__ . '/inc/css-output.php';

// Initialize theme settings.
( new Theme() );
