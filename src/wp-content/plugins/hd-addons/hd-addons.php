<?php
/**
 * Plugin Name: HD Addons
 * Plugin URI: https://webhd.vn
 * Version: 0.24.7
 * Requires PHP: 8.2
 * Author: HD Team
 * Author URI: https://webhd.vn
 * Text Domain: hd-addons
 * Description: Addons plugin for HD Theme
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Requires Plugins: advanced-custom-fields-pro
 */

\defined( 'ABSPATH' ) || die;

$default_headers = [
	'Name'       => 'Plugin Name',
	'Version'    => 'Version',
	'TextDomain' => 'Text Domain',
	'Author'     => 'Author',
];

$plugin_data = get_file_data( __FILE__, $default_headers, 'plugin' );

define( 'ADDONS_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) . '/' );       // https://**/wp-content/plugins/**/
define( 'ADDONS_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR );     // **\wp-content\plugins\**\
define( 'ADDONS_BASENAME', plugin_basename( __FILE__ ) ); // **/**.php

define( 'ADDONS_VERSION', $plugin_data['Version'] );
define( 'ADDONS_TEXT_DOMAIN', $plugin_data['TextDomain'] );
define( 'ADDONS_AUTHOR', $plugin_data['Author'] );

const ADDONS_SRC_PATH = ADDONS_PATH . 'src' . DIRECTORY_SEPARATOR;

if ( ! file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	wp_die( __( 'Error locating autoloader. Please run <code>composer install</code>.', ADDONS_TEXT_DOMAIN ) );
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

register_activation_hook( __FILE__, 'activation' );
register_deactivation_hook( __FILE__, 'deactivation' );
register_uninstall_hook( __FILE__, 'uninstall' );

// The code that runs during plugin activation.
function activation(): void {
	\Addons\Activator\Activator::activation();
}

// The code that runs during plugin deactivation.
function deactivation(): void {
	\Addons\Activator\Activator::deactivation();
}

// The code that will be executed when the plugin is uninstalled.
function uninstall(): void {
	\Addons\Activator\Activator::uninstall();
}

/**
 * @return void
 */
function addons_requirement_notice(): void {
	if ( ! check_plugin_active( 'advanced-custom-fields-pro/acf.php' ) ) {
		printf(
			'<div class="%1$s"><p>%2$s <a target="_blank" href="%3$s"><strong>%4$s</strong></a></p></div>',
			'notice notice-error',
			wp_kses( __( '<strong>HD Addons</strong> plugin requires', ADDONS_TEXT_DOMAIN ), [ 'strong' => [] ] ),
			'https://www.advancedcustomfields.com/',
			esc_html__( 'Advanced Custom Fields PRO', ADDONS_TEXT_DOMAIN )
		);
	}
}

add_action( 'admin_notices', 'addons_requirement_notice' );

/**
 *  Global function-holder. Works similar to a singleton's instance().
 *
 * @return void
 */
function hd_addons(): void {
	require_once __DIR__ . '/Addons.php';

	( new \Addons() );
}

\hd_addons();
