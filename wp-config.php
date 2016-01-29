<?php
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
define('DB_NAME', 'GooseMobile');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost:8888');

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
define('AUTH_KEY',         '-?y/X0V<gYOy,^$q[|7 xkmH!W`0)]ug4Fs@I.EV7*qB.a*nI{>uOP$}Y[,n1j0u');
define('SECURE_AUTH_KEY',  'iev+AHFK86YbUo%+.YF|TDTw7Y/Z*Q(MMKfhas~/7f6SngEW@bWar|+;rMH],-A0');
define('LOGGED_IN_KEY',    '*mzupsR|F~,m,%LGTm.lI]LQ,T|K4d/Jh=0<&1h3uyRw cZ|R@zexL-0=te3vOrT');
define('NONCE_KEY',        'wD|H<v;6_[hz(YFKQt?G3Sh(pViv^eRC#ISqohw~(Izq^p55_jg-uFPjH$L&n12D');
define('AUTH_SALT',        'Jq|[24B=m1^M!ZxfM==-+9&#y1*FG|N3]f+#]!~BNMd|et/ M3ryC4D^@?]7&+lU');
define('SECURE_AUTH_SALT', ':^%?54|3Nm|L:V/LygJ,^xNf@-#B&|xl70?;UB++b7nm4%S5%R|GXFW7=uOw#4$%');
define('LOGGED_IN_SALT',   'L#tF?-yPx>Lzy |ap-)yyX33e$Mp:4$@8IP^@oY|_YR4*uZbdj{.-Uj)+~@!D|zD');
define('NONCE_SALT',       '2~IZt:+2 |{HjS0Az5Mg)PzRqSeL6|iy,P)#^+2L:drE/~XW^=2(WsLo!+_2PmF ');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'gm_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
