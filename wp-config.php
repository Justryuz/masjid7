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
define( 'DB_NAME', 'masjidalmujahideen_db' );

/** Database username */
define( 'DB_USER', 'roots' );

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
define( 'AUTH_KEY',         '?o}ix fsV33AQw{1~^^BJFM+k%qW^d(pal4EjJ_Pfd7rk5^XPRQie[QF_ V|8zBT' );
define( 'SECURE_AUTH_KEY',  'Lq^#y>yJ6J^Jcik`mgt8)h$cqV&&vmTsn=1=gJFhj#D7s~$4{v8YNqhZ59IerL:>' );
define( 'LOGGED_IN_KEY',    'M>,Y=U:BPbum(}pBk6aUKM`Q^g%+CI6U5m&&WtwK<%~]q]Em^ozK8,YZ%CM|o6qx' );
define( 'NONCE_KEY',        'J<M7[%u6p%oY<NuHg.F5:m?Va#qCZ1oGvvGcI*E:W`%i`2l|Vi<;rP# ^sB<3Ww=' );
define( 'AUTH_SALT',        '45eh~f,!=(*e~T:^sEfs[~V?T@JMCcyt%1um)E%37wC3ErIk&z^Ip*A)h&b)>*5g' );
define( 'SECURE_AUTH_SALT', 'B?]YdNhRGJ[Lt4TBL{Xb9pLd/_Y 1T$g>G`GD/n$r{C.ZWj]U&;f2~>-N`J_b[M_' );
define( 'LOGGED_IN_SALT',   'f%e6FVa(a6w3^N]1J4W_:;RFj1L&DAYdV=OzzI!D5a|Vu9VV9.A:%C}X:XG%E`px' );
define( 'NONCE_SALT',       'XgY.E18MtVyYt-7NT+1zvC$-!TY8|3eL=5UdmyJN];dTlN{8f&q?c%^3`_Pfm`y;' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
