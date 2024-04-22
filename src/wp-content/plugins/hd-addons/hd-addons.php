<?php
/**
 * Plugin Name: HD addons
 * Plugin URI: https://webhd.vn
 * Description: Addons plugin for HD Theme
 * Version: 0.24.04
 * Requires PHP: 8.2
 * Author: HD Team
 * Author URI: https://webhd.vn
 * Text Domain: hd-addons
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

\defined( 'ABSPATH' ) || die;

$default_headers = [
	'Name'       => 'Plugin Name',
	'Version'    => 'Version',
	'TextDomain' => 'Text Domain',
	'Author'     => 'Author',
];

$plugin_data = get_file_data( __FILE__, $default_headers, 'plugin' );

define( 'ADDONS_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) ) . '/';       // https://**/wp-content/plugins/hd-addons/
define( 'ADDONS_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) ) . DIRECTORY_SEPARATOR;     // **\wp-content\plugins\hd-addons\
define( 'ADDONS_BASENAME', plugin_basename( __FILE__ ) ); // hd-addons/hd-addons.php

define( 'ADDONS_VERSION', $plugin_data['Version'] );
define( 'ADDONS_TEXT_DOMAIN', $plugin_data['TextDomain'] );
define( 'ADDONS_AUTHOR', $plugin_data['Author'] );

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	wp_die( __( 'Error locating autoloader. Please run <code>composer install</code>.', ADDONS_TEXT_DOMAIN ) );
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Addons.php';

$addons = new Addons();
