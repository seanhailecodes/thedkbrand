<?php
/**
 * Appearance Settings
 *
 * @package Blossom_Shop
 */

function blossom_shop_customize_register_appearance( $wp_customize ) {
    
    $wp_customize->add_panel( 
        'appearance_settings', 
        array(
            'title'       => __( 'Appearance Settings', 'blossom-shop' ),
            'priority'    => 25,
            'capability'  => 'edit_theme_options',
            'description' => __( 'Customize Typography, Background Image & Color.', 'blossom-shop' ),
        ) 
    );

    /** Primary Color*/
    $wp_customize->add_setting( 
        'primary_color', 
        array(
            'default'           => '#dde9ed',
            'sanitize_callback' => 'sanitize_hex_color',
        ) 
    );

    $wp_customize->add_control( 
        new WP_Customize_Color_Control( 
            $wp_customize, 
            'primary_color', 
            array(
                'label'       => __( 'Primary Color', 'blossom-shop' ),
                'description' => __( 'Primary color of the theme.', 'blossom-shop' ),
                'section'     => 'colors',
                'priority'    => 5,
            )
        )
    );
    
    /** Secondary Color*/
    $wp_customize->add_setting( 
        'secondary_color', 
        array(
            'default'           => '#ee7f4b',
            'sanitize_callback' => 'sanitize_hex_color'
        ) 
    );

    $wp_customize->add_control( 
        new WP_Customize_Color_Control( 
            $wp_customize, 
            'secondary_color', 
            array(
                'label'       => __( 'Secondary Color', 'blossom-shop' ),
                'description' => __( 'Secondary color of the theme.', 'blossom-shop' ),
                'section'     => 'colors',
            )
        )
    );
    
    /** Typography */
    $wp_customize->add_section(
        'typography_settings',
        array(
            'title'    => __( 'Typography', 'blossom-shop' ),
            'priority' => 20,
            'panel'    => 'appearance_settings',
        )
    );
    
    /** Primary Font */
    $wp_customize->add_setting(
        'primary_font',
        array(
            'default'           => 'Nunito Sans',
            'sanitize_callback' => 'blossom_shop_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Blossom_Shop_Select_Control(
            $wp_customize,
            'primary_font',
            array(
                'label'       => __( 'Primary Font', 'blossom-shop' ),
                'description' => __( 'Primary font of the site.', 'blossom-shop' ),
                'section'     => 'typography_settings',
                'choices'     => blossom_shop_get_all_fonts(),  
            )
        )
    );
    
    /** Secondary Font */
    $wp_customize->add_setting(
        'secondary_font',
        array(
            'default'           => 'Cormorant',
            'sanitize_callback' => 'blossom_shop_sanitize_select'
        )
    );

    $wp_customize->add_control(
        new Blossom_Shop_Select_Control(
            $wp_customize,
            'secondary_font',
            array(
                'label'       => __( 'Secondary Font', 'blossom-shop' ),
                'description' => __( 'Secondary font of the site.', 'blossom-shop' ),
                'section'     => 'typography_settings',
                'choices'     => blossom_shop_get_all_fonts(),  
            )
        )
    );
    
    /** Move Background Image section to appearance panel */
    $wp_customize->get_section( 'colors' )->panel              = 'appearance_settings';
    $wp_customize->get_section( 'colors' )->priority           = 10;
    $wp_customize->get_section( 'background_image' )->panel    = 'appearance_settings';
    $wp_customize->get_section( 'background_image' )->priority = 15;  
}
add_action( 'customize_register', 'blossom_shop_customize_register_appearance' );