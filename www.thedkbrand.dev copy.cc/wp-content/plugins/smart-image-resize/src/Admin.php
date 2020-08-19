<?php

namespace WP_Smart_Image_Resize;

/**
 * Class WP_Smart_Image_Resize\Settings
 *
 * @package WP_Smart_Image_Resize\Inc
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

if ( ! class_exists( '\WP_Smart_Image_Resize\Settings' ) ) :
    class Admin
    {

        protected static $instance = null;

        /**
         * @return Admin
         */
        public static function get_instance()
        {
            if ( is_null( static::$instance ) ) {
                static::$instance = new Admin;
            }

            return static::$instance;
        }

        public function init()
        {
            // Add plugin to WooCommerce menu.
            add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
            add_filter( 'pre_update_option_wp_sir_settings', [ $this, 'pre_update_settings' ], 10, 2 );
            // Show Woocommerce not installed notice.
            //add_action('admin_notices', [ $this, 'woocommerce_not_installed_notice']);
            add_action( 'admin_notices', [ $this, 'fileinfo_not_enabled' ] );
            add_action( 'admin_notices', [ $this, 'phpversion_not_supported' ] );

            /* block-f */
            add_action( 'admin_notices', [ $this, 'quota_exceeding_soon' ] );
            add_action( 'admin_notices', [ $this, 'quota_exceeded_notice' ] );
            /* #block-f */
            // Initialise settings form.
            add_action( 'admin_init', [ $this, 'init_settings' ] );

            // Add settings help tab.
            //add_action( 'contextual_help', [ $this, 'settings_help' ], 5, 3 );

            add_filter( 'plugin_action_links_' . WP_SIR_BASENAME, [
                $this,
                'plugin_links'
            ] );

            add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ] );
        }

        function quota_exceeding_soon()
        {
            if ( wp_sir_is_quota_exceeding_soon() ) { ?>
                <div class="notice notice-warning is-dismissible">
                    <p><?php _e(
                            'Smart Image Resize: Your are reaching your limit for re-sizing images.',
                            WP_SIR_NAME
                        ); ?>
                        <a href="https:/sirplugin.com/#pro?utm_source=plugin&utm_campaign=notice_limit"
                           class="button button-default"><?php _e(
                                'Upgrade to Pro'
                            ); ?></a> for
                        unlimited images.
                    </p>
                </div>
            <?php }
        }

        function quota_exceeded_notice()
        {
            if ( wp_sir_is_quota_exceeded() ) { ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e(
                            'Smart Image Resize: Your have reached your limit for re-sizing images.',
                            WP_SIR_NAME
                        ); ?>
                        <a href="https:/sirplugin.com/#pro?utm_source=plugin&utm_campaign=notice_limit"
                           class="button button-default"><?php _e(
                                'Upgrade to Pro'
                            ); ?></a> for
                        unlimited images.
                    </p>
                </div>
            <?php }
        }

        function admin_footer_text()
        {
            $screen = get_current_screen();
            if ( $screen->id === 'woocommerce_page_wp-smart-image-resize' ) { ?>
                <?php
                /* block-f */
                ?>
                Please leave us a <a href="https://wordpress.org/support/plugin/smart-image-resize/reviews/">★★★★★
                    rating</a>. We appreciate your support!
                <?php
                /* #block-f */
                ?>
                <?php
                /* block-p */
/* #block-p */
                ?>
            <?php }
        }

        function plugin_links( $links )
        {

            $settings_url    = admin_url( 'admin.php?page=wp-smart-image-resize' );
            $settings_anchor = '<a href="' . $settings_url . '">' . __( 'Settings' ) . '</a>';
            array_unshift( $links, $settings_anchor );
            /* block-f */
            $links[] = '<a href="https://sirplugin.com/?utm_source=plugin&utm_medium=installed_plugins&utm_campaign=go_pro" target="_blank" style="font-weight:bold;color:#38b2ac">Go Pro</a>';

            /* #block-f */

            return $links;
        }

        function pre_update_settings( $newval, $oldval )
        {

            // set boolean values;
            $newval = wp_parse_args( $newval, [
                'enable'      => 0,
                'jpg_convert' => 0,
                'enable_webp' => 0,
                'enable_trim' => 0
            ] );

            return $newval;
        }

        /**
         * Show notice when WooCommerce isn't installed.
         *
         * @return void
         */
        public function woocommerce_not_installed_notice()
        {
            if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) : ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e(
                            'Smart Image Resize requires WooCommerce to work correctly. Please install the WooCommerce Plugin first.',
                            WP_SIR_NAME
                        ); ?></p>
                </div>
            <?php endif;
        }

        public function fileinfo_not_enabled()
        {
            if ( ! extension_loaded( 'fileinfo' ) ) : ?>
                <div class="notice notice-error  is-dismissible">
                    <p><?php _e(
                            'Smart Image Resize: PHP Fileinfo extension is not enabled, contact your hosting provider to enable it.',
                            WP_SIR_NAME
                        ); ?></p>
                </div>
            <?php endif;
        }

        public function phpversion_not_supported()
        {
            if ( ! version_compare( PHP_VERSION, '5.4.0', '>=' ) ) : ?>
                <div class="notice notice-error  is-dismissible">
                    <p><?php _e(
                            'Smart Image Resize requires PHP 5.4.0 or greater to work correctly.',
                            WP_SIR_NAME
                        ); ?></p>
                </div>
            <?php endif;
        }

        /**
         * Add plugin submenu to WooCommerce menu.
         *
         * @return void
         */
        public function add_admin_menu()
        {
            $parent_slug = 'woocommerce';
            $cap         = 'manage_woocommerce';
            if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                $parent_slug = 'options-general.php';
                $cap         = 'manage_options';
            }

            add_submenu_page(
                $parent_slug,
                'Smart Image Resize',
                'Smart Image Resize',
                $cap,
                WP_SIR_NAME,
                [ $this, 'settings_page' ]
            );
        }

        /**
         * Initialize settings form.
         *
         * @return void
         */
        public function init_settings()
        {
            register_setting( WP_SIR_NAME, 'wp_sir_settings' );

            // General section.
            add_settings_section(
                'wp_sir_settings_general',
                __( 'General', WP_SIR_NAME ),
                null,
                WP_SIR_NAME
            );

            // Register `Enable/Disable` plugin resize field.
            add_settings_field(
                'wp_sir_settings_enable',
                __( 'Enable Re-sizing', WP_SIR_NAME ),
                [ $this, 'settings_field_enable' ],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

            // Register `Sizes` field.
            add_settings_field(
                'wp_sir_settings_sizes',
                __( 'Sizes', WP_SIR_NAME ),
                [ $this, 'settings_field_sizes' ],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

            // Register `Enable WebP format` field.
            add_settings_field(
                'wp_sir_settings_enable_trim',
                __( 'Trim whitespace', WP_SIR_NAME ),
                [ $this, 'settings_field_enable_trim' ],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

            // Register `Background Color` field.
            add_settings_field(
                'wp_sir_settings_bg_color',
                __( 'Background Color', WP_SIR_NAME ),
                [ $this, 'settings_field_bg_color' ],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

            // Register `Image Compression` field.
            add_settings_field(
                'wp_sir_settings_image_quality',
                __( 'Image Compression', WP_SIR_NAME ),
                [ $this, 'settings_field_image_quality' ],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

            // Register `Convert to JPG format` field.
            add_settings_field(
                'wp_sir_settings_jpg_convert',
                __( 'Convert to JPG format', WP_SIR_NAME ),
                [ $this, 'settings_field_jpg_convert' ],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

            // Register `Enable WebP format` field.
            add_settings_field(
                'wp_sir_settings_enable_webp',
                __( 'Enable WebP format', WP_SIR_NAME ),
                [ $this, 'settings_field_enable_webp' ],
                WP_SIR_NAME,
                'wp_sir_settings_general'
            );

        }

        function settings_field_enable_trim()
        {
            $settings = \wp_sir_get_settings(); ?>
            <label for="wp-sir-enable-trim">
                <input type="checkbox"
                       name="wp_sir_settings[enable_trim]" <?php checked( $settings[ 'enable_trim' ], 1 ); ?>
                       id="wp-sir-enable-trim" class="wp-sir-as-toggle" value="1"/>
            </label>
            <p class="description">
                <?php _e( 'Remove unwanted whitespace around image.', 'wp-smart-image-resize' ); ?>
            </p>
            <?php
        }

        function settings_field_jpg_convert()
        {
            $settings = \wp_sir_get_settings(); ?>
            <label for="wp-sir-jpg-convert">
                <input type="checkbox"
                       name="wp_sir_settings[jpg_convert]" <?php checked( $settings[ 'jpg_convert' ], 1 ); ?>
                       id="wp-sir-jpg-convert" class="wp-sir-as-toggle" <?php
                /* block-f */
                ?> disabled <?php
                /* #block-f */
                ?> value="1"/>
                <?php
                /* block-f */
                ?>
                <a href="https://sirplugin.com?utm_source=plugin&utm_medium=upgrade&utm_campaign=jpg_convert"><?php _e(
                        'Upgrade to PRO',
                        WP_SIR_NAME
                    ); ?></a>
                <?php
                /* #block-f */
                ?>
            </label>
            <p class="description">
                <?php _e(
                    "Converting images to JPG format is highly recommended to boost page load time.",
                    WP_SIR_NAME
                ); ?>
            </p>
            <?php
        }

        function settings_field_enable_webp()
        {
            $settings = \wp_sir_get_settings(); ?>
            <label for="wp-sir-enable-webp">
                <input type="checkbox"
                       name="wp_sir_settings[enable_webp]" <?php checked( $settings[ 'enable_webp' ], 1 ); ?>
                       id="wp-sir-enable-webp" class="wp-sir-as-toggle" <?php
                /* block-f */
                ?> disabled <?php
                /* #block-f */
                ?> value="1"/>
                <?php
                /* block-f */
                ?>
                <a href="https://sirplugin.com?utm_source=plugin&utm_medium=upgrade&utm_campaign=enabled_webp"><?php _e(
                        'Upgrade to PRO',
                        WP_SIR_NAME
                    ); ?></a>
                <?php
                /* #block-f */
                ?>

            </label>
            <p class="description">
                <?php _e(
                    "WebP reduces image file size by up to 90% comparing to PNG images without losing quality.",
                    WP_SIR_NAME
                ); ?>
            </p>
            <?php

            /* block-p */
/* #block-p */
            ?>
            <?php
        }

        public function settings_field_image_quality( $args )
        {
            $settings = \wp_sir_get_settings(); ?>
            <input name="wp_sir_settings[jpg_quality]" type="hidden" class="wpSirImageQuality"
                   value="<?php echo absint( $settings[ 'jpg_quality' ] ); ?>"/>
            <div class="wpSirSlider" style="width:300px" data-input="wpSirImageQuality">
                <div class="wpSirSliderHandler ui-slider-handle ppsir-slider-handle"></div>
            </div>
            <?php
        }

        function settings_field_sizes()
        {
            $settings = \wp_sir_get_settings(); ?>
            <select multiple="multiple" id="wpSirResizeSizes" name="wp_sir_settings[sizes][]">
                <?php foreach ( wp_sir_get_image_sizes() as $key => $size ) : ?>
                    <option value="<?php echo $key; ?>" <?php echo in_array(
                        $key,
                        $settings[ 'sizes' ]
                    )
                        ? 'selected'
                        : ''; ?>><?php echo "$key({$size['width']}x{$size['height']})"; ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">
                <?php _e(
                    "Select which thumbnails sizes to generate.",
                    WP_SIR_NAME
                ); ?>
            </p>
            <?php
        }

        public function settings_field_bg_color( $args )
        {
            $settings = \wp_sir_get_settings(); ?>
            <input name="wp_sir_settings[bg_color]" value="<?php echo $settings[ 'bg_color' ]; ?>" type="text"
                   id="wpSirColorPicker"/>
            <p class="description"><?php _e(
                    'Leave empty for transparent background.',
                    WP_SIR_NAME
                ); ?></p>
            <?php
        }

        public function settings_field_enable( $args )
        {
            $settings = \wp_sir_get_settings(); ?>
            <label for="wp-sir-enable">
                <input type="checkbox" class="wp-sir-as-toggle wp-sir-as-toggle--large" name="wp_sir_settings[enable]"
                       id="wp-sir-enable" value="1" <?php checked( $settings[ 'enable' ], 1 ); ?> />
            </label>
            <?php
            /* block-f */
            ?>
            <p class="description" <?php echo wp_sir_is_quota_exceeded()
                ? 'style="color:red"'
                : ''; ?>><?php echo wp_sir_processed_quota(); ?> images of 150 processed. <a
                        href="https://sirplugin.com/#pro">Upgrade to Pro</a> for unlimited images.
            </p>
            <?php
            /* #block-f */
            ?>
            <?php
        }

        public function settings_page()
        {
            include_once WP_SIR_DIR . 'templates/settings.php';
        }

        function settings_help( $old_help, $screen_id, $screen )
        {
            if ( 'woocommerce_page_wp-smart-image-resize' != $screen_id ) {
                return;
            }
            // Add one help tab
            $screen->add_help_tab( array(
                'id'      => 'wp-sir-help-tab1',
                'title'   => esc_html__( 'Overview', WP_SIR_NAME ),
                'content' =>
                    '<p><strong>Sizes:</strong> Select which sizes to generate.</p>' .
                    '<p><strong>Background Color:</strong> set the color of the emerging (empty) area of the generated thumbnail. Leave it empty for transparent background.</p>' .
                    '<p><strong>Image Compression:</strong> Compress images to reduce image file size to improve  page load time.</p>' .
                    '<p><strong>Trim whitespace:</strong> Remove unwanted whitespace around image to make all images look uniform.</p>' .
                    '<p><strong>Convert to JPG format:</strong> If transparent images aren\'t required, it\'s recommanded to convert images to JPG to boost page load speed.</p>' .
                    '<p><strong>Enable WebP format:</strong> WebP is the rockstart of image formats. Using WebP can dramatically reduce image file size without losing the quality of the image. WebP is widely supported by all modern browsers, otherwise, it fallbacks automatically to standard image.</p>'
            ) );

            /* block-p */
/* #block-p */

            /* block-f */
            $help_sidebar = '<p><a href="https://sirplugin.com?utm_source=plugin&utm_medium=upgrade&utm_campaign=help_sidebar">Upgrade to PRO</a></p>' .
                            '<p><a href="https://wordpress.org/support/plugin/smart-image-resize/" target="_blank">Report an issue</a></p>';
            /* #block-f */
            get_current_screen()->set_help_sidebar(
                '<p><strong>' .
                esc_html__( 'For more information:', WP_SIR_NAME ) .
                '</strong></p>' . $help_sidebar
            );

            return $old_help;
        }
    }
endif;
