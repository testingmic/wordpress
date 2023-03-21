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

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'eo^Yg>TYH(<tz4XWFy+eG#:uw{&6ZI#I).0!#nTiX%0{XR0~[*j- (&!}QiZ~:S@' );
define( 'SECURE_AUTH_KEY',  'Kn)~#tj/Ui`zVXUk}#idkSn2,jKlzj;&I,5&tR=3!kdDs1l6G^QTM~?m,1}Ok[&j' );
define( 'LOGGED_IN_KEY',    '<fgycIJMieQ YL|M~jd|:r]vD{jE}rI,oh?30Vqs+AwMU$EYowwBe*yYn88U|-|T' );
define( 'NONCE_KEY',        'P}qk5#T`Tw(&7xIK#Ah.y/7Zo=@`&;P!RVGt&`kj=#2*-Kof-xf`ZrS~gfHBmRGZ' );
define( 'AUTH_SALT',        'V~@b@P~f Xcber5?T5BKs9pnf|``g]gBO2UJK!J5#pLvtZ+Mv{NaX=U]4_JV~rm1' );
define( 'SECURE_AUTH_SALT', '|<O3Oz$r^,(*0lF^ ZPo[:]0w<&kFt$O4-JvGC/wm@Q)#gJx_e)f%+)}&4)ct2QH' );
define( 'LOGGED_IN_SALT',   'K()&2>2 U7 5T~X-}R[~^pqP]Uul7h;Qtfd?@G_joO%X@Pr-SMU)Zc%hHqdcR)3<' );
define( 'NONCE_SALT',       ':rn>a9sdO)$xno[Z:Fg~(xcmPEV=ZTo*B|Zl,BV:h3cb,^T9LvLd5p0#2xb>JMx|' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
