<?php
/* block-f */

/**
 *
 * @link              https://sirplugin.com
 * @since             1.0.0
 * @package           WP_Smart_Image_Resize
 *
 * @wordpress-plugin
 * Plugin Name: Smart Image Resize for WooCommerce
 * Plugin URI: http://wordpress.org/plugins/smart-image-resize
 * Description: Make WooCommerce products images the same size and uniform without cropping.
 * Version: 1.3.7
 * Author: Nabil Lemsieh
 * Author URI: https://sirplugin.com
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.html
 * Text Domain: wp-smart-image-resize
 * Domain Path: /languages
 */
/* #block-f */

/* block-p */
/* #block-p */


define( 'WP_SIR_VERSION', '1.3.7' );
define( 'WP_SIR_NAME', 'wp-smart-image-resize' );
define( 'WP_SIR_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_SIR_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_SIR_BASENAME', plugin_basename( __FILE__ ) );

/* block-p */
/* #block-p */

// Load plugin loader class.
require_once WP_SIR_DIR . 'src/Plugin.php';

// Activate plugin callback.
function wp_sir_activate()
{
    add_option( 'wp_sir_plugin_version', WP_SIR_VERSION );
}

register_activation_hook( __FILE__, 'wp_sir_activate' );

// Initialize plugin.
add_action( 'plugins_loaded', function () {
    \WP_Smart_Image_Resize\Plugin::get_instance()->run();
} );
