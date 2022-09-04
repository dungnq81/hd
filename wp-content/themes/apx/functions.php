<?php

/**
 * Theme functions and definitions
 *
 * @package apx
 */

use APX\Themes\Theme;

if (version_compare($GLOBALS['wp_version'], '5.3', '<')) {

    // This theme requires WordPress 5.3 or later.
    require __DIR__ . 'inc/back-compat.php';
}

$theme_version = ($theme_version = wp_get_theme()->get('Version')) ? $theme_version : false;
$theme_author = ($theme_author = wp_get_theme()->get('Author')) ? $theme_author : 'APX Team';

defined('APX_THEME_VERSION') || define('APX_THEME_VERSION', $theme_version);
defined('APX_AUTHOR') || define('APX_AUTHOR', $theme_author);
defined('APX_THEME_PATH') || define('APX_THEME_PATH', untrailingslashit(get_template_directory()));
defined('APX_THEME_URL') || define('APX_THEME_URL', untrailingslashit(esc_url(get_template_directory_uri())));

if (!file_exists($composer = __DIR__ . '/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'apx'));
}

require $composer;

// Initialize theme settings.
( new Theme )->init();