<?php
/**
 * Footer Setting
 *
 * @package Blossom_Shop
 */

function blossom_shop_customize_register_footer( $wp_customize ) {
    
    $wp_customize->add_section(
        'footer_settings',
        array(
            'title'      => __( 'Footer Settings', 'blossom-shop' ),
            'priority'   => 199,
            'capability' => 'edit_theme_options',
        )
    );

    /** Footer Copyright */
    $wp_customize->add_setting(
        'footer_copyright',
        array(
            'default'           => '',
            'sanitize_callback' => 'wp_kses_post',
            'transport'         => 'postMessage'
        )
    );
    
    $wp_customize->add_control(
        'footer_copyright',
        array(
            'label'       => __( 'Footer Copyright Text', 'blossom-shop' ),
            'section'     => 'footer_settings',
            'type'        => 'textarea',
        )
    );
    
    $wp_customize->selective_refresh->add_partial( 'footer_copyright', array(
        'selector' => '.site-info .copyright',
        'render_callback' => 'blossom_shop_get_footer_copyright',
    ) );
    
    $wp_customize->add_setting( 'footer_payment_image',
        array(
            'default'           => '',
            'sanitize_callback' => 'blossom_shop_sanitize_image',
        )
    );
    
    $wp_customize->add_control( 
        new WP_Customize_Cropped_Image_Control( $wp_customize, 'footer_payment_image',
            array(
                'label'         => esc_html__( 'Payment Support Image', 'blossom-shop' ),
                'section'       => 'footer_settings',
                'type'          => 'cropped_image',
                'width'         => '300px',
                'height'        => '25px'
            )
        )
    );
}
add_action( 'customize_register', 'blossom_shop_customize_register_footer' );