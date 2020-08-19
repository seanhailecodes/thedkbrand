<?php
/**
 * Blossom Shop Theme Customizer
 *
 * @package Blossom_Shop
 */

/**
 * Requiring customizer panels & sections
*/
$blossom_shop_panels     = array( 'info', 'site', 'appearance', 'layout', 'home', 'general', 'footer' );

foreach( $blossom_shop_panels as $p ){
    require get_template_directory() . '/inc/customizer/' . $p . '.php';
}

/**
 * Sanitization Functions
*/
require get_template_directory() . '/inc/customizer/sanitization-functions.php';

/**
 * Active Callbacks
*/
require get_template_directory() . '/inc/customizer/active-callback.php';

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function blossom_shop_customize_preview_js() {
	wp_enqueue_script( 'blossom-shop-customizer', get_template_directory_uri() . '/inc/js/customizer.js', array( 'customize-preview' ), BLOSSOM_SHOP_THEME_VERSION, true );
}
add_action( 'customize_preview_init', 'blossom_shop_customize_preview_js' );

function blossom_shop_customize_script(){
    $array = array(
        'home'    => get_permalink( get_option( 'page_on_front' ) ),
    );
    wp_enqueue_style( 'blossom-shop-customize', get_template_directory_uri() . '/inc/css/customize.css', array(), BLOSSOM_SHOP_THEME_VERSION );
    wp_enqueue_script( 'blossom-shop-customize', get_template_directory_uri() . '/inc/js/customize.js', array( 'jquery', 'customize-controls' ), BLOSSOM_SHOP_THEME_VERSION, true );
    wp_localize_script( 'blossom-shop-customize', 'blossom_shop_cdata', $array );
}
add_action( 'customize_controls_enqueue_scripts', 'blossom_shop_customize_script' );