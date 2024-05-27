<?php
/*!
Plugin Name: MU-HD
Plugin URI: https://webhd.vn
Description: mu-plugin for HD Theme
Version: 0.24.5
Requires PHP: 7.4
Author: HD Team
Author URI: https://webhd.vn
Text Domain: mu-hd
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

$headers = [
	'Name'       => 'Plugin Name',
	'Version'    => 'Version',
	'TextDomain' => 'Text Domain',
];

$plugin_data = get_file_data( __FILE__, $headers, 'plugin' );

define( 'MU_HD_PLUGIN_VERSION', $plugin_data['Version'] );
define( 'MU_HD_PLUGIN_TEXT_DOMAIN', $plugin_data['TextDomain'] );

if ( file_exists( __DIR__ . '/mu-hd/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/mu-hd/vendor/autoload.php';
}
