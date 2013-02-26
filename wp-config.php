<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'abx198469');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         '?XwGx= !On-E H;Bu3zo,G[kFPQPP|l8M!z[[~mqPuyYse+4o8>gwr(3%12y3Y@m');
define('SECURE_AUTH_KEY',  'iHeo)_V9NU7N(fXNk8<|}<mvJwYMu|r-xFlGQ3Dd[>%4_)uC1038u|i%h^$/z7>_');
define('LOGGED_IN_KEY',    '~D4cWb)9[HXUx`6d%;rdMc7R;Q$&*~MwI7WkyCw~Y|<.,M:3goF#D@b=+?X2O&[,');
define('NONCE_KEY',        'f9<R(?c^g^VktA$A(Vi+S4>Vd)~M;y[|;$6_7+ms/|24S>5d[!jCzlNg:0!l%fZN');
define('AUTH_SALT',        '9qXtW#+;U~AE&?@E7Py-Z|/`aV8*fnX.=iFw(tJw-nIF+zxTrV>U({C#-ViZ~np(');
define('SECURE_AUTH_SALT', 'hWyH`Zq<hY1EWlrQ)hVk>`:&Ir7, [xc+MLRPu1V++#wb)~eFBiy}#?{{/uu%-v~');
define('LOGGED_IN_SALT',   'r0f;&S4U,yYJT5WDPLg)#$R`MV2L=12bK.bX^+a<S5v YH|>e#<+@oxN+tSC<&|/');
define('NONCE_SALT',       '|f+6T-ra)Dr`2lV7*a^|bY<4m)rB UM~0?f:0%biI{6Z&h,P0-TqP6b/e(8I,0gX');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
