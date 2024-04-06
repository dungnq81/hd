<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'hd' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         'e}*IAp`9.~.Q(vekI?Bm(o4Yt sxM}$6kNJ0f5^D;vb;ylm^vJ*~N@]6fTmhMce ' );
define( 'SECURE_AUTH_KEY',  '&sdJLe_ugr]}Wu%^npQoZ;R~DR^{oNEOvA#rOJ?DSf)P;jAGB>eGAQXry?j+n6J^' );
define( 'LOGGED_IN_KEY',    '9Mn~vg4X#xg8k.GcQ<0+o3>=pwr1c4_)<!iI 5q7#F.2PO7Qkx54L^xb2_XxgH~(' );
define( 'NONCE_KEY',        ' KLig-gF/U</H:<l/d]1/3yLGVzEs j/05:z(ON*aPXbLF0a&4rR~];>`T@s9Z |' );
define( 'AUTH_SALT',        'Y)yEJ.uQ$7DrF]f)o?mxj)Zrr _ (!/L*KBxgL7@W:|^rw]F+OqNzD;FGN.,<gmR' );
define( 'SECURE_AUTH_SALT', '$)lO-C9jXfYZI-7E2~Jh?m)2Q9}]3#]&$?Rld l=MW9]d~5T$<uDLzW N!v}YdI0' );
define( 'LOGGED_IN_SALT',   'Vjb4oXw{ys_!f1AtXc~]QWKKZXb6Fu#TNXugTC7t#?=jGFKwBacdF%/Y~JI`^&J*' );
define( 'NONCE_SALT',       '#Qv$d`p&V1^9.I,qN]rbajw P_}o!_BLx+cM=TM$+~mM),-Z!UOFlT345W]&9L5t' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
