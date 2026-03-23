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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'S,tLB)+19DUo{L]r`vQ!=sOtk:P*_wl0JO,d!KI>oDGCc4V1M<j]4ME{9^E[tuH2' );
define( 'SECURE_AUTH_KEY',   'VYrWq5j+APO$dV;rLA3wL/xSCjDB~nA5<4;IGF0_X#_8#!BMVn)LO01H:|9lxf$f' );
define( 'LOGGED_IN_KEY',     '@0;sL/=W/fNL;SDLA`9b<<|D~ig+UL41Q`x c,l2Uj_4hAP1lD&LW%:fJwtX=Sg!' );
define( 'NONCE_KEY',         'GfBDQRIxTH#z/;?BN1OvVNavxMyU*fAY:?|#mivCex3xG2m68sO31qcP7B/VM~Fa' );
define( 'AUTH_SALT',         'qxfzr[?%]nb-2P<vL?[a} 2,k*?:du6Wz M|G3j C<G^ULsn]qM.^;Jda9VWi7hy' );
define( 'SECURE_AUTH_SALT',  '@HuSzutw%4J:A|!Eh0|CE)6j1 iD+x`0jT3^d.r=s<@gR4l9$6su*e(tj,ru@5-|' );
define( 'LOGGED_IN_SALT',    'b!]kmJ`.i/dE{&p+bh$A4Greb*jJ_n:[*xW#Aku@R8Kioz:9Uswz^IyjYzz-2nEj' );
define( 'NONCE_SALT',        'j>%As@>_[Y,zAh^5Z83_|w`0WdV%#= !p&dBUe~dtPYvk&8n[eKl/kreB}JT%UsL' );
define( 'WP_CACHE_KEY_SALT', ']SgyNv $VR`JZM5hdBl%A$[}_Xi,;(_|4I}b[p94zw^q1)p^g$Ogy!-}k*OjR18#' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
