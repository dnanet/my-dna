<?php

define('WP_CACHE', true); // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'dna_db');

/** MySQL database username */
define('DB_USER', 'dna_user');

/** MySQL database password */
define('DB_PASSWORD', '1!qaz2@wsx');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '`LI:r@VA3S|A/wutbBok2U{21,nLpK?m9c^N3b!KjRr:,B<j?evPXMJ]K!LN5MQO');
define('SECURE_AUTH_KEY',  'm=FKvV.VScXy_2AV/^ +Ju28{DY1~.>W6Egefg-rV.2HuGy+48AD})B kGWz^L%H');
define('LOGGED_IN_KEY',    'kF!)OO[Y[pl%T,cTabiti3kvD-oGaUYkMF~,QrX8 4F[W}0GYqD X Uog1{Gs$2I');
define('NONCE_KEY',        'u@W<c}_s#SNkt)G@!HRN@O)v)3k#XKs{S^M?mS)BLNcM5DKoU/ U8O?M-?$<Dh{6');
define('AUTH_SALT',        'mYjNkfg+>TnZ^#^,H@_9s`(Vrg7>].ou$RI?O(hE;Z6LxGc-]dQmuCMjZo f8x49');
define('SECURE_AUTH_SALT', '(z5;z41_S#j {4-$qCV3S?*R|bgwM;`D1PS_W-88M!k;21p,{:F$ij3S,/f7jWMB');
define('LOGGED_IN_SALT',   '4ypO0HaR4}lZ|I?ThXXdwHZU3?epVhqk{AmHwh!f5d)zVX=}`i/>jSg:k?>)Fj5`');
define('NONCE_SALT',       'JAeB/%L{Js =DwQOu|-pWKwjIn@ WGCq$Uw?.elMp(G;wA s0d]o-sw(Oij_K}.(');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_MEMORY_LIMIT', '128M');
//define('WP_DEBUG', false);
// Enable WP_DEBUG mode
define( 'WP_DEBUG', false );

// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', false );

// Disable display of errors and warnings
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
