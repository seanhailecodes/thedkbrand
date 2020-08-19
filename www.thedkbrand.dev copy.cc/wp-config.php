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
define( 'DB_NAME', 'thedkbraDBzw7lf');

/** MySQL database username */
define( 'DB_USER', 'thedkbraDBzw7lf');

/** MySQL database password */
define( 'DB_PASSWORD', 'uDWInwhnZ3');

/** MySQL hostname */
define( 'DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 e
 * @since 2.6.0
 */
define( 'AUTH_KEY', '|H-]Sh:Gh-GKdp.#m#6lp*Pe]9ap5LiyEXb{AauxHL+*2etAPbqIMy{Tj36Pq.<X');
define( 'SECURE_AUTH_KEY', 'GOk8k|Nd4ds-#d]5t_K_:l-CS+DS]6i9Ox2apHbqHX*Xm;P+<e]6fq6u.Mm2E$IT*');
define( 'LOGGED_IN_KEY', 'mfE$fqIP>frU}Q3B>>Un4Jvj08!F[0zC|ckCh8!:kNsVd1aDK:l:lOZtLpSaHlO#');
define( 'NONCE_KEY', 'AuP;ei2H#IMy{qAQq.j2Ii+My}Mc{r^QU^3yUY,7@BU^fz3Jy>Z[0cCV!|UkJz@5O');
define( 'AUTH_SALT', 'kV:;t#Ga#l~KO~:s;2exW#5Wp5+]ae+;uETu<m2Lm+L${bf{E.Pj.6iIX37j$b<Bn');
define( 'SECURE_AUTH_SALT', 'a2p.uEIu<m6Lm*L{EbfE,UX.7$Ib${N}BrvF,3jn3M{Jc@}cBR!,U4Jz>-:s8Rs!');
define( 'LOGGED_IN_SALT', '2<7IbnEn$j{7juh;9hxa#1Zp1~:HT*]x9Px_p2Hp~2*;Xi{x<Pa<2*3Iq$IEq$');
define( 'NONCE_SALT', 'oNZ>C0gvFYNo_SGZ[C1hwGZO@1hVo8RGHa]~1h-p9S_w#Wp9:Gw[H6T.2#Wp9;Hx]');

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', true );
define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );