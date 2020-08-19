<?php


if ( ! function_exists( 'wp_sir_get_settings' ) ) :
    /**
     * Get plugin settings
     *
     * @return array
     */
    function wp_sir_get_settings()
    {
        $defaults = [
            'enable'      => 1,
            'bg_color'    => '#ffffff',
            'jpg_quality' => 5,
            'sizes'       => array_keys( wp_sir_get_additional_sizes() ),
            'jpg_convert' => 0,
            'enable_webp' => 0,
            'enable_trim' => 0,
        ];
        _wp_sir_set_compat_settings();

        $settings = wp_parse_args( get_option( 'wp_sir_settings' ), $defaults );

        if ( empty( $settings[ 'sizes' ] ) ) {
            $settings[ 'sizes' ] = array_keys( wp_sir_get_additional_sizes() );
        }

        /* block-p */
/* #block-p */


        return $settings;
    }
endif;

if ( ! function_exists( '_wp_sir_set_compat_settings' ) ) {

    /**
     * Backward compatibility with old versions settings.
     */
    function _wp_sir_set_compat_settings()
    {

        $settings = get_option( 'wp_sir_settings' ) ?: [];
        if ( ! empty( $settings ) ) {
            return;
        }

        $legacy_settings = get_option( 'ppsir_settings' );
        if ( empty( $legacy_settings ) ) {
            return;
        }

        $settings[ 'enable' ] = isset( $legacy_settings[ 'enable' ] )
                                && $legacy_settings[ 'enable' ]
            ? 1 : 0;

        if ( isset( $legacy_settings[ 'bg_color' ] ) ) {
            $settings[ 'bg_color' ] = $legacy_settings[ 'bg_color' ];
        }
        if ( isset( $legacy_settings[ 'jpg_quality' ] ) ) {
            $settings[ 'jpg_quality' ] = 100 - absint( $legacy_settings[ 'jpg_quality' ] );
        }

        add_option( 'wp_sir_settings', $settings );
        delete_option( 'ppsir_settings' );
    }
}


/**
 * Get working images sizes
 *
 * @return array
 */
if ( ! function_exists( 'wp_sir_get_image_sizes' ) ) :
    function wp_sir_get_image_sizes()
    {
        $sizes = wp_get_additional_image_sizes();
        foreach ( [ 'thumbnail', 'medium', 'medium_large', 'large' ] as $name ) {
            $sizes[ $name ] = [
                'width'  => absint( get_option( "{$name}_size_w" ) ),
                'height' => absint( get_option( "{$name}_size_h" ) ),
            ];
        }

        foreach ( $sizes as $name => $data ) {
            if ( absint( $data[ 'width' ] ) === 0 ) {
                $sizes[ $name ][ 'width' ] = $sizes[ $name ][ 'height' ];
            } elseif ( absint( $data[ 'height' ] ) === 0 ) {
                $sizes[ $name ][ 'height' ] = $sizes[ $name ][ 'width' ];
            }
        }

        return $sizes;
    }
endif;

if ( ! function_exists( 'wp_sir_get_size_dimensions' ) ) :

    /**
     * Get a given size name dimensions
     *
     * @param string $name
     *
     * @return array|null
     */
    function wp_sir_get_size_dimensions( $name )
    {
        $size = null;

        foreach ( wp_sir_get_image_sizes() as $n => $data ) {
            if ( $n === $name ) {
                $size = $data;
                break;
            }
        }

        if (
            ! $size || ! isset( $size[ 'width' ], $size[ 'height' ] )
            || min( $size[ 'width' ], $size[ 'height' ] ) === 0
        ) {
            return null;
        }

        return $size;
    }
endif;


if ( ! function_exists( 'wp_sir_get_upload_dir' ) ) {

    /**
     * Get WP uploads directory path
     *
     * @param string $file
     *
     * @return string
     */
    function wp_sir_get_upload_dir( $file = "" )
    {
        return wp_get_upload_dir()[ 'basedir' ] . ( $file === "" ? '' : '/' . $file );
    }
}


if ( ! function_exists( 'wp_sir_get_additional_sizes' ) ) :

    /**
     * Get sizes except default ones.
     *
     * @return array
     */
    function wp_sir_get_additional_sizes()
    {
        $sizes        = wp_sir_get_image_sizes();
        $custom_sizes = [];
        foreach ( $sizes as $k => $size ) {
            if ( ! in_array( $k, array(
                'thumbnail',
                'medium',
                'medium_large',
                'large',
                '1536x1536',
                '2048x2048',
                'shop_single',
                'shop_catalog',
                'shop_thumbnail'
            ) ) ) {
                $custom_sizes[ $k ] = $size;
            }
        }

        return $custom_sizes;
    }
endif;


if ( ! function_exists( 'wp_sir_webp_supported' ) ) {

    /**
     * Check if WebP format is supported by browser.
     *
     * @return bool
     */
    function wp_sir_is_webp_supported()
    {
        return ( ! wp_is_mobile() && strpos( $_SERVER[ 'HTTP_ACCEPT' ], 'image/webp' ) !== false );
    }
}

if ( ! function_exists( 'wp_sir_update_quota' ) ) {
    function wp_sir_update_quota( $attachment_id )
    {
        $attachments_count = get_option( 'wp_sir_processed_attachments' ) ?
        array_filter((array)get_option( 'wp_sir_processed_attachments' ))
        : [];
        if ( isset( $attachments_count[ $attachment_id ] ) ) {
            $attachments_count[ $attachment_id ]++;
        } else {
            $attachments_count[ $attachment_id ] = 1;
        }
        update_option( 'wp_sir_processed_attachments', $attachments_count );
    }
}

if ( ! function_exists( 'wp_sir_is_quota_exceeded' ) ) {
    function wp_sir_is_quota_exceeded()
    {
        $count_total = wp_sir_processed_quota();

        $value = $count_total > 150;

        /* block-f */
        /* block-p */
/* #block-p */

        /* #block-f */

        return $value;

    }
}

if ( ! function_exists( 'wp_sir_processed_quota' ) ) {

    /**
     * Update quota
     *
     */
    function wp_sir_processed_quota()
    {
        $attachments_count = get_option( 'wp_sir_processed_attachments' ) ?
        array_filter((array)get_option( 'wp_sir_processed_attachments' ))
        : [];

        return array_reduce( $attachments_count, function ( $count, $cur ) {
            return $count + absint( $cur );
        }, 0 );
    }
}
if ( ! function_exists( 'wp_sir_is_quota_exceeding_soon' ) ) {

    /**
     * Returns true if the quota is exceeding soon
     *
     */
    function wp_sir_is_quota_exceeding_soon()
    {
        $count_total = wp_sir_processed_quota();

        return $count_total > 100 && $count_total < 150;
    }
}


if ( ! function_exists( 'wp_sir_is_attached_to' ) ) {

    /**
     * Returns true if the given attachment is attached to the given post type.
     *
     * @param int $attachment_id
     * @param string|array|null $post_type
     *
     * @return bool
     */
    function wp_sir_is_attached_to( $attachment_id, $post_type = null )
    {

        if ( empty( $attachment_id ) ) {
            return false;
        }

        if ( ! get_post( $attachment_id ) ) {
            return false;
        }

        if ( empty( $post_type ) ) {
            return true;
        }

        $post_type = (array)$post_type;

        global $wpdb;

        $post_type_placeholder = implode( ',', array_fill( 0, count( $post_type ), '%s' ) );

        $sql = "SELECT pm.post_id from {$wpdb->postmeta} pm
        JOIN {$wpdb->posts} p
        ON p.ID = pm.post_id
        WHERE pm.meta_key IN ('_thumbnail_id','_product_image_gallery')
        AND FIND_IN_SET(%d, pm.meta_value)
        AND p.post_type IN($post_type_placeholder)";

        $post_ids = $wpdb->get_col( $wpdb->prepare( $sql, array_merge( [ $attachment_id ], $post_type ) ) );

        $post_ids = array_unique( array_filter( $post_ids ) );

        return apply_filters( 'wp_sir_is_attached_to', count( $post_ids ) > 0, $attachment_id, $post_type, $post_ids );

    }

    if ( ! function_exists( 'wp_sir_is_imagick_installed' ) ) {

        /**
         * Return true if ImageMagick is installed on the server.
         */
        function wp_sir_is_imagick_installed()
        {
            return ( extension_loaded( 'imagick' ) && class_exists( 'Imagick' ) );
        }
    }

    if ( ! function_exists( 'wp_sir_is_webp_installed' ) ) {
        /**
         * Return true if WebP is installed on the server.
         * @return bool
         */
        function wp_sir_is_webp_installed()
        {
            return function_exists( 'imagewebp' );
        }
    }

    if ( ! function_exists( 'wp_sir_regen_thumb_active' ) ) {
        function wp_sir_regen_thumb_active()
        {
            return in_array( 'regenerate-thumbnails/regenerate-thumbnails.php',
                apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
        }
    }
}

function wp_sir_get_regeneratable_images()
{
    global $wpdb;
    $post_type = (array)apply_filters( 'wp_sir_resize_post_type', 'product' );

    if ( empty( $post_type ) ) {
        return [];
    }

    $post_type_placeholder = implode( ',', array_fill( 0, count( $post_type ), '%s' ) );

    $post_status = (array)apply_filters( 'wp_sir_regeneratable_post_status', 'publish' );

    $sql                          = "SELECT pm.meta_value FROM $wpdb->posts p 
            LEFT JOIN $wpdb->postmeta pm 
            ON pm.post_id = p.ID 
            WHERE p.post_type IN ($post_type_placeholder)
            AND p.post_status = '%s'
            AND ( pm.meta_key = '_thumbnail_id' OR pm.meta_key = '_product_image_gallery' )";
    $attachment_ids               = $wpdb->get_col( $wpdb->prepare( $sql, array_merge( $post_type, $post_status ) ) );
    $regeneratable_attachment_ids = [];

    foreach ( $attachment_ids as $id ) {
        $regeneratable_attachment_ids = array_merge( $regeneratable_attachment_ids, explode( ',', $id ) );
    }

    return array_unique( array_map( 'intval', $regeneratable_attachment_ids ) );

}