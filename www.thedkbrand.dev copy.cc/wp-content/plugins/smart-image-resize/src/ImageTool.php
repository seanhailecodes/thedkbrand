<?php

namespace WP_Smart_Image_Resize;

use \Exception;
use \Intervention\Image\ImageManager;

/*
 * Class WP_Smart_Image_Resize\ImageTool
 *
 * @package WP_Smart_Image_Resize\Inc
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( '\WP_Smart_Image_Resize\ImageTool' ) ) :
    final  class ImageTool
    {
        protected static $instance = null;
        protected $manager;

        /**
         * @return ImageTool
         */
        public static function get_instance()
        {
            if ( is_null( static::$instance ) ) {
                static::$instance = new ImageTool;
            }

            return static::$instance;
        }

        public function __construct()
        {
            $config        = [ 'driver' => apply_filters( 'wp_sir_driver', 'gd' ) ];
            $this->manager = new ImageManager( $config );
        }

        /**
         * Register hooks.
         */
        public function init()
        {
            /**
             * Start manipulation.
             */
            add_filter( 'wp_generate_attachment_metadata', [ $this, 'manipulate_image' ], PHP_INT_MAX, 2 );

            add_filter( 'wp_get_attachment_metadata', [ $this, 'force_original_metadata' ], PHP_INT_MAX, 2 );
            add_filter( 'wp_get_attachment_image_src', [ $this, 'load_webp' ], PHP_INT_MAX, 4 );
            add_filter( 'wp_calculate_image_srcset_meta', [ $this, 'set_srcset' ], PHP_INT_MAX, 4 );
            /**
             * Delete WebP images after deleting an image.
             */
            add_action( 'delete_attachment', [ __CLASS__, 'deleteWebPImages' ] );
            /**
             * Prevent WooCommerce from regenerating thumbnails on the fly.
             */
            add_filter( 'woocommerce_image_sizes_to_resize', '__return_empty_array' );

            /**
             * Prevent Jetpack from regenerating thumbnails on the fly.
             */
            add_filter( 'jetpack_photon_skip_image', '__return_true' );

            /**
             * Add selected sizes to WC background regenerate.
             */

            add_filter( 'woocommerce_regenerate_images_intermediate_image_sizes', [ $this, 'addSelectedSizes' ] );

            /**
             * Auto-add "product_variation" to allowed post types.
             */
            add_filter( 'wp_sir_resize_post_type', [ $this, 'add_product_variation_posttype' ], PHP_INT_MAX );
        }

        function add_product_variation_posttype( $post_type )
        {
            $post_type = (array)$post_type;
            if ( in_array( 'product', $post_type, true ) ) {
                $post_type[] = 'product_variation';
            }

            return $post_type;

        }

        /**
         * Filter regeneratable images.
         *
         * @param array $args
         * @param \WP_REST_Request $request
         *
         * @return array
         */
        function filter_regeneratable_images( $args, $request )
        {

            try {


                if ( ! $request->get_param( 'is_regeneratable' ) ) {
                    return $args;
                }

                if ( ! $this->is_resize_canvas_enabled() ) {
                    return $args;
                }

                if ( count( $regeneratable_attachment_ids = wp_sir_get_regeneratable_images() ) === 0 ) {
                    $regeneratable_attachment_ids = [ -1 ];
                }

                $args[ 'post__in' ] = empty( $args[ 'post__in' ] )
                    ? $regeneratable_attachment_ids
                    : array_merge( (array)$args[ 'post__in' ], $regeneratable_attachment_ids );

            } catch ( Exception $e ) {
            }

            return $args;
        }

        /**
         * Prevent optimization plugins (e.g Jetpack) from regenerating thumbnails on the fly.
         *
         * @param array $metadata
         * @param int $attachmentId
         *
         * @return array
         */
        function force_original_metadata( $metadata, $attachmentId )
        {

            if ( ! apply_filters( 'wp_sir_force_original_metadata', true ) ) {
                return $metadata;
            }

            if ( ! $this->is_processable( $metadata, $attachmentId ) ) {
                return $metadata;
            }

            $originalMetadata = get_post_meta( $attachmentId, '_wp_attachment_metadata', true );

            if ( ! apply_filters( 'wp_sir_serve_old_thumbnails', false ) ) {

                if(isset($originalMetadata[ 'sizes' ]))
               {
                foreach ( array_keys( $originalMetadata[ 'sizes' ] ) as $size ) {
                    if ( strpos( $size, '_old_' ) > 0 ) {
                        unset( $originalMetadata[ 'sizes' ][ $size ] );
                    }
                }
               }
            }

            return is_array( $originalMetadata ) && ! empty( $originalMetadata ) ? $originalMetadata : $metadata;
        }

        function is_processable( $metadata, $attachmentId )
        {
            return $this->is_resize_canvas_enabled()
                   && wp_attachment_is_image( $attachmentId )
                   && ! empty( $metadata );
        }

        /**
         * Proceed resize.
         *
         * @param array $metadata
         * @param int $attachment_id
         *
         * @return array
         */
        public function manipulate_image( $metadata, $attachment_id )
        {

            try {
                if ( ! $this->is_processable( $metadata, $attachment_id ) ) {
                    return $metadata;
                }

                /* block-f */
                if ( wp_sir_is_quota_exceeded() ) {
                    return $metadata;
                }
                /* #block-f */

                if ( ! $this->is_current_post_type_allowed( $attachment_id ) ) {
                    return $metadata;
                }

                $originalImageBasePath = get_attached_file( $attachment_id );

                // In most cases, the original image file may not be found on server.
                if ( ! is_readable( $originalImageBasePath ) ) {
                    return $metadata;
                }

                $originalImage = $this->manager->make( $originalImageBasePath );

                /**
                 * Remove whitespace.
                 */
                $this->trimWhitespace( $originalImage );

                /* block-p */
/* #block-p */

                /*
                 * Resize to the selected sizes.
                 */

                $selectedSizes = $this->getSelectedSizes( $attachment_id );

                foreach ( $selectedSizes as $sizeKey => $size ) {
                    $image                 = clone $originalImage;
                    $destThumbnailBasePath = $this->getDestThumbnailBasePath( $image, $size );

                    /*
                     Resize to a given size while preserving the aspect-ratio.
                     */

                    $image->resize( $size[ 'width' ], $size[ 'height' ], function (
                        $constraint
                    ) {
                        $constraint->aspectRatio();
                    } );

                    /*
                     Create a canvas that will hold the image.
                    */
                    $canvas = $this->manager->canvas(
                        $size[ 'width' ],
                        $size[ 'height' ],
                        $this->getCanvasColor() );

                    /*
                    Add the image to the canvas.
                    */
                    $canvas->insert( $image, 'center' );


                    /*
                     * Clean up old thumbnails.
                     */
                    $this->deleteOldThumbnail( $metadata, $sizeKey );

                    $newSize = [
                        'width'  => $size[ 'width' ],
                        'height' => $size[ 'height' ]
                    ];

                    /*
                     * Maybe convert to JPG.
                     */
                    if ( $this->can_convert_to_jpg( $image ) ) {
                        $jpgCanvas            = clone $canvas;
                        $jpgThumbnailDestPath = Helpers::replaceFileExtension( $destThumbnailBasePath, 'jpg' );
                        $jpgCanvas->save( $jpgThumbnailDestPath, $this->getQuality() );

                        $newSize = array_merge( $newSize, [
                            'file'      => $jpgCanvas->basename,
                            'mime-type' => $jpgCanvas->mime()
                        ] );

                    } else {
                        $canvas->save( $destThumbnailBasePath );
                        $newSize = array_merge( $newSize, [
                            'file'      => $canvas->basename,
                            'mime-type' => $canvas->mime()
                        ] );

                    }

                    $newSizes[ $sizeKey ] = $newSize;

                    /* block-p */
/* #block-p */
                }

                $metadata[ 'sizes' ] = array_merge( $metadata[ 'sizes' ], $newSizes );
                update_post_meta( $attachment_id, '_attachment_resized', current_time( 'mysql' ) );

                /* block-f */
                wp_sir_update_quota( $attachment_id );
                /* #block-f */
                @wp_cache_flush();

                return $metadata;
            } catch ( Exception $e ) {
                wp_send_json_error( [
                    'message' => "Smart Image Resize: {$e->getMessage()}",
                ] );
            }
        }


        /**
         * Check if current post typed is allowed.
         * Note: Experimental
         *
         * @param $attachmentId
         *
         * @return bool
         */
        function is_current_post_type_allowed( $attachmentId )
        {
            $postType = apply_filters( 'wp_sir_resize_post_type', 'product', $attachmentId );

            return $this->is_attached_post_type( $attachmentId, $postType );
        }

        /**
         * Return true if we can convert to JPG.
         *
         * @param \Intervention\Image\Image $image
         *
         * @return bool
         */
        function can_convert_to_jpg( $image )
        {
            return $this->isConvertToJPGEnabled() && ! Helpers::isJPG( $image->extension );
        }

        /**
         * Returns quality setting.
         * @return int
         */
        function getQuality()
        {
            return 100 - absint( wp_sir_get_settings()[ 'jpg_quality' ] );
        }

        /**
         * Return sizes to resize.
         *
         * @param $attachmentId
         *
         * @return array
         */
        function getSelectedSizes( $attachmentId )
        {
            $sizesKeys = apply_filters(
                'wp_sir_sizes',
                wp_sir_get_settings()[ 'sizes' ],
                $attachmentId
            );

            $sizes = [];
            foreach ( $sizesKeys as $sizeKey ) {
                $sizes[ $sizeKey ] = wp_sir_get_size_dimensions( $sizeKey );
            };

            return array_filter( $sizes );
        }

        /**
         * Return canvas color.
         * @return string|null
         */
        function getCanvasColor()
        {
            return sanitize_hex_color( wp_sir_get_settings()[ 'bg_color' ] ) ?: null;
        }

        /**
         * Match src.
         *
         * @param array $metadata
         * @param array $imageArr
         * @param string $imageSrc
         * @param int $attachmentId
         *
         * @return array
         */
        function set_srcset( $metadata, $imageArr, $imageSrc, $attachmentId )
        {
            try {

                // WebP isn't enabled/supported.
                if ( ! $this->can_load_webp( $attachmentId ) ) {
                    return $metadata;
                }

                // No meta.
                if ( empty( $metadata ) ) {
                    return $metadata;
                }

                $imageInfo = pathinfo( $metadata[ 'file' ] );

                foreach ( $metadata[ 'sizes' ] as $sizeName => $sizeData ) {
                    $thumbInfo     = pathinfo( $sizeData[ 'file' ] );
                    $thumbBasePath = wp_sir_get_upload_dir( "{$imageInfo['dirname']}/{$thumbInfo['filename']}.webp" );
                    if ( is_readable( $thumbBasePath ) ) {
                        $metadata[ 'sizes' ][ $sizeName ][ 'file' ] = $thumbInfo[ 'filename' ] . '.webp';
                    }
                }

                $webPBasePath = wp_sir_get_upload_dir( $imageInfo[ 'dirname' ] . '/' . $imageInfo[ 'filename' ] . '.webp' );

                if ( is_readable( $webPBasePath ) ) {
                    $metadata[ 'file' ] = $imageInfo[ 'dirname' ] . '/' . $imageInfo[ 'filename' ] . '.webp';
                }

                return $metadata;

            } catch ( Exception $e ) {
                return $metadata;
            }
        }

        /**
         * @param \Intervention\Image\Image $image
         *
         * @param string $extension
         * @param array $size
         *
         * @return string
         */
        function getDestThumbnailBasePath( $image, $size, $extension = null )
        {
            $dirname = dirname( $image->basePath() );

            $extension = is_null( $extension ) ? $image->extension : $extension;

            return sprintf( '%s/%s-%dx%d.%s',
                $dirname,
                $image->filename,
                $size[ 'width' ],
                $size[ 'height' ],
                $extension );
        }

        /**
         *
         * Remove old thumbnail to clean up Media Library.
         *
         * @param array $metadata
         * @param string $size_key
         *
         * @return void
         *
         */
        function deleteOldThumbnail( $metadata, $size_key )
        {
            if ( ! isset( $metadata[ 'sizes' ][ $size_key ] ) ) {
                return;
            }

            $upload_dir = wp_sir_get_upload_dir();
            $sub_dir    = dirname( $metadata[ 'file' ] );

            $image_path = $upload_dir . '/' . $sub_dir . '/' . $metadata[ 'sizes' ][ $size_key ][ 'file' ];

            if ( file_exists( $image_path ) ) {
                @unlink( $image_path );
            }
        }

        /**
         * @return bool
         */
        public function isConvertToJPGEnabled()
        {
            return wp_sir_get_settings()[ 'jpg_convert' ];
        }

        /**
         * @param int $attachment_id
         * @param string|null $post_type
         *
         * @return bool
         */
        public function is_attached_post_type( $attachment_id, $post_type = null )
        {
            if ( empty( $post_type ) ) {
                return true;
            }

            $post_type = (array)$post_type;

            $referer = wp_get_referer();

            if ( ! $referer ) {
                return false;
            }

            foreach ( $post_type as $item ) {
                if ( false !== strpos( $referer, 'post_type=' . $item ) ) {
                    return true;
                }
            }


            $url = wp_parse_url( $referer );

            $defaults = [
                'path'  => '',
                'query' => '',
            ];

            $url = wp_parse_args( $url, $defaults );

            if ( false !== strpos( $url[ 'path' ], 'post.php' ) ) {

                if ( empty( $url[ 'query' ] ) ) {
                    return false;
                }
                /**
                 * @var array $params
                 */
                wp_parse_args( wp_parse_str( $url[ 'query' ], $params ), [
                    'post' => -1,
                ] );

                return in_array( get_post_type( $params[ 'post' ] ), $post_type );
            }

            return wp_sir_is_attached_to( $attachment_id, $post_type );
        }

        /**
         * Returns true if the resize functionality is enabled.
         *
         * @return bool
         */
        private function is_resize_canvas_enabled()
        {
            return wp_sir_get_settings()[ 'enable' ];
        }

        /**
         * Use WebP if enabled and supported by the browser.
         * Fallback to standard format.
         *
         * @param array|false $image
         * @param int $attachment_id
         * @param string|array $size
         * @param bool $icon
         *
         * @return array|false
         */
        public function load_webp( $image, $attachment_id, $size, $icon )
        {
            if ( ! $this->can_load_webp( $attachment_id ) || ! $image ) {
                return $image;
            }

            list( $url ) = $image;

            // Get generated sizes metadata.
            $metadata = wp_get_attachment_metadata( $attachment_id );

            // If by change there is no metadata, abort.
            if ( empty( $metadata ) ) {
                return $image;
            }

            if ( is_array( $size ) ) {
                $intermediate_size = image_get_intermediate_size(
                    $attachment_id,
                    $size
                );

                if ( ! $intermediate_size ) {
                    return $image;
                }

                $_size = array_filter( $metadata[ 'sizes' ], function ( $file ) use (
                    $intermediate_size
                ) {
                    return $intermediate_size[ 'file' ] === $file[ 'file' ];
                } );

                if ( empty( $_size ) ) {
                    return $image;
                }

                $_size = array_keys( $_size );
                $size  = array_shift( $_size );
            }

            if ( 'full' === $size ) {
                $origin_file       = pathinfo( $metadata[ 'file' ] );
                $webPImageBasePath = wp_sir_get_upload_dir(
                    "{$origin_file['dirname']}/{$origin_file['filename']}.webp"
                );
                $this->maybeCreateWebPImage(
                    $webPImageBasePath,
                    wp_sir_get_upload_dir( $metadata[ 'file' ] )
                );
                if ( ! file_exists( $webPImageBasePath ) ) {
                    return $image;
                }
                $image[ 0 ] = str_replace(
                    $origin_file[ 'extension' ],
                    'webp',
                    $url
                );

                return $image;
            } elseif ( isset( $metadata[ 'sizes' ][ $size ] ) ) {
                $filename       = pathinfo( $metadata[ 'sizes' ][ $size ][ 'file' ], PATHINFO_FILENAME );
                $attachment_dir = dirname( $metadata[ 'file' ] );

                // Expected WebP thumbnail basename.
                $webPImageBasePath = wp_sir_get_upload_dir( "$attachment_dir/$filename.webp" );
                $this->maybeCreateWebPImage(
                    $webPImageBasePath,
                    wp_sir_get_upload_dir(
                        "$attachment_dir/{$metadata['sizes'][$size]['file']}"
                    )
                );

                if ( ! file_exists( $webPImageBasePath ) ) {
                    return $image;
                }

                $url        = str_replace( basename( $url ), "$filename.webp", $url );
                $image[ 0 ] = $url;
            }

            return $image;
        }

        /**
         * Determine whether to load WebP images.
         *
         * @param int $attachmentId
         *
         * @return bool
         */
        public function can_load_webp( $attachmentId )
        {
            $can_load_webp = wp_sir_get_settings()[ 'enable_webp' ]
                             && wp_sir_is_webp_supported()
                             && wp_sir_is_attached_to( $attachmentId,
                    apply_filters( 'wp_sir_resize_post_type', 'product', $attachmentId ) )
                             && ! is_admin();

            return apply_filters( 'wp_sir_load_webp', $can_load_webp );
        }

        /**
         * Generate WebP format image if it doesn't exist.
         *
         * @param string $webp_file
         * @param string $source_file
         *
         * @return void
         */
        private function maybeCreateWebPImage( $webp_file, $source_file )
        {
            if ( is_readable( $webp_file ) || ! function_exists( 'imagewebp' ) ) {
                return;
            }
            try {
                @$image = $this->manager->make( $source_file );
                @$image->save( $webp_file );
            } catch ( Exception $e ) {
                return;
            }
        }

        /**
         * Delete WebP linked to the given attachment.
         *
         * @param int $attachment_id
         */
        public static function deleteWebPImages( $attachment_id )
        {
            if ( ! wp_attachment_is_image( $attachment_id ) ) {
                return;
            }

            $metadata = wp_get_attachment_metadata( $attachment_id );

            $origin_file = pathinfo( $metadata[ 'file' ] );

            // Delete WebP thumbnails.
            foreach ( $metadata[ 'sizes' ] as $size ) {
                $filename = pathinfo( $size[ 'file' ], PATHINFO_FILENAME );
                wp_delete_file( wp_sir_get_upload_dir( "{$origin_file['dirname']}/$filename.webp" ) );
            }

            // Delete original WebP image.
            $basePath = "{$origin_file['dirname']}/{$origin_file['filename']}.webp";
            wp_delete_file( wp_sir_get_upload_dir( $basePath ) );
        }

        /**
         * Returns true if WebP is enabled by user
         * and installed on the server.
         *
         * @return bool
         */
        function isWebPEnabled()
        {
            return wp_sir_get_settings()[ 'enable_webp' ] && function_exists( 'imagewebp' );
        }

        /**
         * Return true if whitespace trimming is enabled.
         *
         * @return bool
         */
        function isTrimEnabled()
        {
            return wp_sir_get_settings()[ 'enable_trim' ];
        }

        /**
         * Remove empty white and gray background from the given image
         *
         * @param \Intervention\Image\Image $image
         *
         * @return void
         */
        function trimWhitespace( &$image )
        {
            if ( ! $this->isTrimEnabled() ) {
                return;
            }

            try {
                $image->trim(
                    apply_filters( 'wp_sir_trim_base', null ),
                    apply_filters( 'wp_sir_trim_away', null ),
                    apply_filters( 'wp_sir_trim_tolerance', 5 ),
                    apply_filters( 'wp_sir_trim_feather', 0 )
                );
            } catch ( Exception $e ) {
                // Abort silently.
            }
        }
    }

endif;
