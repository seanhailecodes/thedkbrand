<?php
/**
 * Layout Settings
 *
 * @package Blossom_Shop
 */

function blossom_shop_customize_register_layout( $wp_customize ) {

    /** Layout Settings */
    $wp_customize->add_panel( 
        'layout_settings',
         array(
            'priority'    => 30,
            'capability'  => 'edit_theme_options',
            'title'       => __( 'Layout Settings', 'blossom-shop' ),
            'description' => __( 'Change different page layout from here.', 'blossom-shop' ),
        ) 
    );

    /** Blog Page Layout Settings */
    $wp_customize->add_section(
        'blog_layout',
        array(
            'title'    => __( 'Blog Page Layout', 'blossom-shop' ),
            'priority' => 40,
            'panel'    => 'layout_settings',
        )
    );
    
    /** Page Sidebar layout */
    $wp_customize->add_setting( 
        'blog_page_layout', 
        array(
            'default'           => 'classic-layout',
            'sanitize_callback' => 'blossom_shop_sanitize_radio'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Shop_Radio_Image_Control(
            $wp_customize,
            'blog_page_layout',
            array(
                'section'     => 'blog_layout',
                'label'       => __( 'Blog Page Layout', 'blossom-shop' ),
                'description' => __( 'Choose the blog page layout for your site.', 'blossom-shop' ),
                'choices'     => array(
                    'classic-layout' => get_template_directory_uri() . '/images/blog/classic.jpg',
                    'grid-layout'    => get_template_directory_uri() . '/images/blog/grid.jpg',
                    'list-layout'    => get_template_directory_uri() . '/images/blog/listing.jpg',
                )
            )
        )
    );

    /** General layout Settings */
    $wp_customize->add_section(
        'general_layout_settings',
        array(
            'title'    => __( 'General Sidebar Layout', 'blossom-shop' ),
            'priority' => 55,
            'panel'    => 'layout_settings',
        )
    );
    
    /** Page Sidebar layout */
    $wp_customize->add_setting( 
        'page_sidebar_layout', 
        array(
            'default'           => 'right-sidebar',
            'sanitize_callback' => 'blossom_shop_sanitize_radio'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Shop_Radio_Image_Control(
            $wp_customize,
            'page_sidebar_layout',
            array(
                'section'     => 'general_layout_settings',
                'label'       => __( 'Page Sidebar Layout', 'blossom-shop' ),
                'description' => __( 'This is the general sidebar layout for pages. You can override the sidebar layout for individual page in respective page.', 'blossom-shop' ),
                'choices'     => array(
                    'no-sidebar'    => get_template_directory_uri() . '/images/1c.jpg',
                    'centered'      => get_template_directory_uri() . '/images/1cc.jpg',
                    'left-sidebar'  => get_template_directory_uri() . '/images/2cl.jpg',
                    'right-sidebar' => get_template_directory_uri() . '/images/2cr.jpg',
                )
            )
        )
    );
    
    /** Post Sidebar layout */
    $wp_customize->add_setting( 
        'post_sidebar_layout', 
        array(
            'default'           => 'right-sidebar',
            'sanitize_callback' => 'blossom_shop_sanitize_radio'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Shop_Radio_Image_Control(
            $wp_customize,
            'post_sidebar_layout',
            array(
                'section'     => 'general_layout_settings',
                'label'       => __( 'Post Sidebar Layout', 'blossom-shop' ),
                'description' => __( 'This is the general sidebar layout for posts & custom post. You can override the sidebar layout for individual post in respective post.', 'blossom-shop' ),
                'choices'     => array(
                    'no-sidebar'    => get_template_directory_uri() . '/images/1c.jpg',
                    'centered'      => get_template_directory_uri() . '/images/1cc.jpg',
                    'left-sidebar'  => get_template_directory_uri() . '/images/2cl.jpg',
                    'right-sidebar' => get_template_directory_uri() . '/images/2cr.jpg',
                )
            )
        )
    );
    
    /** Post Sidebar layout */
    $wp_customize->add_setting( 
        'layout_style', 
        array(
            'default'           => 'right-sidebar',
            'sanitize_callback' => 'blossom_shop_sanitize_radio'
        ) 
    );
    
    $wp_customize->add_control(
        new Blossom_Shop_Radio_Image_Control(
            $wp_customize,
            'layout_style',
            array(
                'section'     => 'general_layout_settings',
                'label'       => __( 'Default Sidebar Layout', 'blossom-shop' ),
                'description' => __( 'This is the general sidebar layout for whole site.', 'blossom-shop' ),
                'choices'     => array(
                    'no-sidebar'    => get_template_directory_uri() . '/images/1c.jpg',
                    'left-sidebar'  => get_template_directory_uri() . '/images/2cl.jpg',
                    'right-sidebar' => get_template_directory_uri() . '/images/2cr.jpg',
                )
            )
        )
    );
}
add_action( 'customize_register', 'blossom_shop_customize_register_layout' );