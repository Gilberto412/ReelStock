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
define('AUTH_KEY',         'OrviHq9SXd2Xd5KF05ZwNXtXG96KD7A5ZaEN2WCvfQT051TLOo6G5LOxKYBvNRg5zpYGa/Rm7Qzf3fckouRTEw==');
define('SECURE_AUTH_KEY',  'xp5yWCgZ3y2M+9OJuXO8aDgRi5xJenvJQxFGi7Tlr9/g3K3VFzqec+Ld6K9V8zkRtjJ4wlO+X53xJrlLtHqaBg==');
define('LOGGED_IN_KEY',    'ntYsHp8sdzOeuLvBRHroezOngH6xYy/lgGWuTSvcMB44dwJAP2d1gEWX8KkTXm+vOM559sE2sEHaC/lzzjn5Ag==');
define('NONCE_KEY',        '4usNK/WzV65oZ6FlzlSmLi0O7aVpiV00kpJjSBxGfUKdLPHoICdtMZfXxTx5HWJQWBg+vylqGuHLitpKVDp8Sw==');
define('AUTH_SALT',        'F32LV1QCYHMKlJ3kdFHC6wZGjWfv9FPiNbYvdl8gLIeMOnzCA57sl5w/ydx7Q2pi26Fekl5Z5W8mmOwuY4UCsg==');
define('SECURE_AUTH_SALT', 'bHhgSWQRRIqTb1X3Ez6e20+1ASQOOqFgeKJtB5N6cagnVHt8M27HSZj7fh04mAeY91hLIPUKu3gqBYkGILKPqA==');
define('LOGGED_IN_SALT',   'flp1Jw3webbfyJpZYlJRl/E68NiOdySMb2fmWTgjB9tCrxegbwGPfhiSN92BKuKJoqDwPA3ORgtHIPc8me4B+g==');
define('NONCE_SALT',       'ViOGYinUG+GAKrBCaYkrs7gMX9OFZbck3PS28rTHX7b9hxoqyx0SkPs9ezLN/XG/eeqI/82SDQ9yeNXl9kGICA==');


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
