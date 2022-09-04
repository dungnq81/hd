<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// Main switch to get frontend assets from a Vite dev server OR from production built folder
const VITE_DEVELOPMENT = true;

/** PHP Memory */
const WP_MEMORY_LIMIT     = '512';
const WP_MAX_MEMORY_LIMIT = '512';

const DISALLOW_FILE_EDIT = false;
const DISALLOW_FILE_MODS = false;

/* SSL */
const FORCE_SSL_LOGIN = true;
const FORCE_SSL_ADMIN = true;

const WP_POST_REVISIONS = 1;
const EMPTY_TRASH_DAYS  = 5;
const AUTOSAVE_INTERVAL = 120;

/** Disable WordPress core auto-update, */
const WP_AUTO_UPDATE_CORE = false;

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

const DB_NAME = 'apx';
const DB_USER = 'root';
const DB_PASSWORD = 'root';

const DB_HOST = 'localhost';
const DB_CHARSET = 'utf8mb4';
const DB_COLLATE = '';

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '],ScLh+B$?.No2o$MmOd9y/8ZjDTiE3zCtNszv5~h?u?zTYx{VsE=YOix0SMWdcx' );
define( 'SECURE_AUTH_KEY',  ')0lPGjB:Mpr:bQhe+Wk,zy+v`5z#q~%2CqGexvt:TQ>x1OmRWM!IW-zNob7[</C[' );
define( 'LOGGED_IN_KEY',    'f,^tF!<>YVI(]r[n4|R!fer L,YJy!(Ox$XbV09<6[3$8}{~!yPdTrERcl,4b8|4' );
define( 'NONCE_KEY',        '^Sg4(%WO^U;beyRp?dAaO{6rGhp%/WP<H7REl0w~[OaoS$J#7=r&e!}=$llho)>w' );
define( 'AUTH_SALT',        'j_xJpG7cmz[Nc~xT><vpvnF[8cc]d3>s7bDn}xwVR:GdgR_!D;z4K Z^rG4;15cH' );
define( 'SECURE_AUTH_SALT', 'T_8z); } eJfYX5;faa0t0Ob96*9;_7u6!6]0R_ncZGqV/&7Ag,<=4`l:e{X8#Zx' );
define( 'LOGGED_IN_SALT',   'h2F2,23m#Mw.n}90Q*d~{_.1Ko]sy?s+Q^&tgoWFI!pCu#FQAvo@~nY>5b~%%Nu1' );
define( 'NONCE_SALT',       'L@^P@#|b#o5-pZSI!L(HvG3!fkC&yAM7ix=i1xaN0IQQm5Yf?j ur+A@V{bvEE_^' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'w_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
const WP_DEBUG = false;

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';