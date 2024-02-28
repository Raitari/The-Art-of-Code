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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u2508092_wp907' );

/** Database username */
define( 'DB_USER', 'u2508092_wp907' );

/** Database password */
define( 'DB_PASSWORD', '2p5m-cSu4.' );

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
define( 'AUTH_KEY',         'tovsjqp9gwbar8dlwjz0tlyxnvqys77yobdu8kmuinbvhm3i7oum9e2d7uav8pvz' );
define( 'SECURE_AUTH_KEY',  'glcxhtrjxpp80vhslbenjxex0slcmehho9yv3mnr1lh598xwaa3zvbgcs8fe9swo' );
define( 'LOGGED_IN_KEY',    'ab1hx6cyhmqh8fs49v4g8n07f9lhsvgylr7gz5lqcsdj3kdvrmzmfawl8fqczmb2' );
define( 'NONCE_KEY',        'jxdzilmwpdoexs3d1buuxfti2j2wfwiuboaxr1dllkkul2gc0x7dw8fucjp06dce' );
define( 'AUTH_SALT',        'c55zuayj6usanvvvjfiaucyxigsu54ajzxtrw0m9bogqtsefk5oqo0sznkm0sdip' );
define( 'SECURE_AUTH_SALT', 'vbe8dt4j4eof3qxtybwibonjmcg73fzvvb5djaepooyqjdhlyudoudts3hfqfgot' );
define( 'LOGGED_IN_SALT',   'qyw62n4rwclmc6trdnuwug83khowg3jfoypzasevkwzvfb4v8qjoxiluprunvljt' );
define( 'NONCE_SALT',       'vfc5mfchqfgz2x6mdhyuc2mjhk17wsmodhdwsmunxac0ds9jry4h6p3nqdwpbdwi' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp9x_';

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
